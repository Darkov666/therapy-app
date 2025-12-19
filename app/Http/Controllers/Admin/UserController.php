<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Inertia\Inertia;
use Illuminate\Support\Facades\Validation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\PsychologistAccountApproved;
use App\Mail\PsychologistAccountDenied;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = User::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nickname', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                $query->where('role', $role);
            });

        // Filtering based on authenticated user role
        if ($user->role === 'psychologist' || $user->role === 'titular') {
            $query->where(function ($q) use ($user) {
                // Users created by this psychologist
                $q->where('created_by', $user->id)
                    // OR patients assigned via appointments
                    ->orWhereHas('appointments', function ($subQ) use ($user) {
                        $subQ->where('psychologist_id', $user->id);
                    })
                    // OR themselves
                    ->orWhere('id', $user->id);
            });
        }

        $users = $query->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'nickname' => $u->nickname,
                'role' => $u->role,
                'is_approved' => $u->is_approved,
                'created_at' => $u->created_at->format('d/m/Y'),
                'profile_photo_url' => $u->profile_photo_url,
                'created_by' => $u->created_by,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
            'can_create' => in_array($user->role, ['admin', 'titular', 'psychologist']),
        ]);
    }

    public function create()
    {
        $creator = auth()->user();
        $allowedRoles = [];

        if ($creator->role === 'admin') {
            $allowedRoles = [
                ['value' => 'patient', 'label' => 'Paciente'],
                ['value' => 'psychologist', 'label' => 'Psicólogo'],
                ['value' => 'titular', 'label' => 'Titular'],
                ['value' => 'admin', 'label' => 'Administrador'],
            ];
        } elseif ($creator->role === 'titular') {
            $allowedRoles = [
                ['value' => 'patient', 'label' => 'Paciente'],
                ['value' => 'psychologist', 'label' => 'Psicólogo'],
            ];
        } elseif ($creator->role === 'psychologist') {
            $allowedRoles = [
                ['value' => 'patient', 'label' => 'Paciente'],
            ];
        }

        return Inertia::render('Admin/Users/Create', [
            'allowed_roles' => $allowedRoles,
        ]);
    }

    public function store(Request $request)
    {
        $creator = auth()->user();

        // Allowed roles logic (replicated for validation)
        $validRoles = [];
        if ($creator->role === 'admin')
            $validRoles = ['admin', 'titular', 'psychologist', 'patient'];
        elseif ($creator->role === 'titular')
            $validRoles = ['psychologist', 'patient'];
        elseif ($creator->role === 'psychologist')
            $validRoles = ['patient'];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($validRoles)],
            'phone' => 'nullable|string|max:20',
            'specialty' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nickname' => $validated['nickname'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'specialty' => $validated['specialty'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'is_approved' => true,
            'created_by' => $creator->id,
            'must_change_password' => true, // Force password change on first login
        ]);

        // Send Email Verification
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            // Log error but continue
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => $user->load('appointments'), // Load other relationships as needed
        ]);
    }

    public function edit(User $user)
    {
        $this->authorizeAction($user);

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAction($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nickname' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:patient,psychologist,admin',
            'specialty' => 'nullable|string',
            'is_approved' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAction($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleStatus(User $user)
    {
        $this->authorizeAction($user);

        $user->update([
            'is_approved' => !$user->is_approved
        ]);

        // Send Notification if Psychologist
        if ($user->role === 'psychologist') {
            if ($user->is_approved) {
                Mail::to($user)->send(new PsychologistAccountApproved($user));
            } else {
                Mail::to($user)->send(new PsychologistAccountDenied($user));
            }
        }

        $status = $user->is_approved ? 'activado' : 'desactivado';
        return back()->with('success', "El usuario ha sido {$status}.");
    }

    private function authorizeAction(User $targetUser)
    {
        $currentUser = auth()->user();

        if ($currentUser->role === 'admin') {
            return true;
        }

        if ($currentUser->role === 'titular') {
            // Titular can manage psychologists and patients?
            // For now, let's restrict to created_by or maybe all for Titular if they are "Head"?
            // Safer to stick to created_by OR specific roles (e.g. Titular can manage all Psychologists/Patients)
            // Let's allow Titular to manage anyone except Admins.
            if ($targetUser->role === 'admin') {
                abort(403, 'Unauthorized action on Admin.');
            }
            return true;
        }

        if ($currentUser->role === 'psychologist') {
            // Can only manage users they created
            if ($targetUser->created_by !== $currentUser->id) {
                // Allow managing self? No, usually profile page for that.
                if ($targetUser->id === $currentUser->id)
                    return true;

                abort(403, 'No tienes permiso para modificar este usuario.');
            }
            return true;
        }

        // Default deny
        abort(403);
    }
}
