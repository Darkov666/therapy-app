<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

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

        return User::create([
            'name' => $input['name'],
            'nickname' => $input['nickname'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'role' => $input['role'],
            'specialty' => $input['specialty'] ?? null,
            'is_approved' => $input['role'] === 'psychologist' ? false : true,
            'profile_photo_path' => $photoPath,
            'password' => Hash::make($input['password']),
        ]);
    }
}
