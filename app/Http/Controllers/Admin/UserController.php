<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Inertia\Inertia;
use Illuminate\Support\Facades\Validation;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%");
            })
            ->when($request->role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nickname' => $user->nickname,
                'role' => $user->role,
                'is_approved' => $user->is_approved,
                'created_at' => $user->created_at->format('d/m/Y'),
                'profile_photo_url' => $user->profile_photo_url,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => $user->load('appointments'), // Load other relationships as needed
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
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
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleStatus(User $user)
    {
        $user->update([
            'is_approved' => !$user->is_approved
        ]);

        $status = $user->is_approved ? 'activado' : 'desactivado';
        return back()->with('success', "El usuario ha sido {$status}.");
    }
}
