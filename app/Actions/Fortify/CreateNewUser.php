<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPsychologistNotification;
use App\Mail\PsychologistRegistrationPending;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Honeypot check
        if (!empty($input['fax'])) {
            abort(403, 'Spam detected.');
        }

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in(['patient', 'psychologist'])],
            'specialty' => ['nullable', 'string', 'required_if:role,psychologist'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'terms' => ['accepted', 'required'],
            'password' => $this->passwordRules(),
        ])->validate();

        $photoPath = null;
        if (isset($input['photo'])) {
            $photoPath = $input['photo']->store('profile-photos', 'public');
        }

        // Identify creator/owner
        $creatorId = null;
        // Default to Main Admin for main page registrations
        // We look for the "Titular" / Admin defined in seeder
        $adminOptions = User::where('email', 'juliana@therapy.app')->orWhere('role', 'admin')->first();
        if ($adminOptions) {
            $creatorId = $adminOptions->id;
        }

        $user = User::create([
            'name' => $input['name'],
            'nickname' => $input['nickname'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'role' => $input['role'],
            'specialty' => $input['specialty'] ?? null,
            'is_approved' => $input['role'] === 'psychologist' ? false : true,
            'profile_photo_path' => $photoPath,
            'password' => Hash::make($input['password']),
            'created_by' => $creatorId,
        ]);

        if ($input['role'] === 'psychologist') {
            // Notify Yuliana and Super Admin
            $admins = User::whereIn('role', ['admin', 'titular'])->get();
            // Also include specific email if not found in roles (fallback)
            $specificAdmin = User::where('email', 'juliana@therapy.app')->first();
            $recipients = $admins->merge([$specificAdmin])->filter()->unique('id');

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send(new NewPsychologistNotification($user));
            }

            // Notify the new Psychologist
            Mail::to($user)->send(new PsychologistRegistrationPending($user));
        }

        return $user;
    }
}
