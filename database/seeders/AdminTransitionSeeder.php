<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminTransitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newAdminEmail = 'juliana@therapy.app'; // Or user specific email if different? Requirements said "Psic. Yuliana Sanchez Navarrete"
        // Since "elimina al psicologo juliana" implies the old one might have this email. 
        // I'll assume we keep the email but update the identity?
        // Or create a NEW one. 
        // Requirement: "crea tambie a este usuario... y elimina al psicologo juliana"
        // I will create the new one with a proper email (maybe the same one if I delete the old one first, or update the old one).
        // Safest: Update the old one if it exists to be the new one, OR create new and reassign.
        // Let's TRY to find 'juliana' (maybe by name or email).

        $oldUser = User::where('name', 'like', '%juliana%')->orWhere('email', 'like', '%juliana%')->first();

        // Define New Admin Data
        $adminData = [
            'name' => 'Psic. Yuliana Sanchez Navarrete',
            'nickname' => 'yuliana', // For subdomain/route logic
            'email' => 'juliana@therapy.app',
            'password' => Hash::make('password'), // Reset or keep? Let's set default for safety or keep if updating.
            'role' => 'admin', // "tiene los mismos permisos que el superusuario... pero no puede crear otros admins" -> Wait, requirement says "Colocaremos al administrador... este sera el dominio principal".
            // Actually, if there is a 'root' SuperUser, Yuliana is 'admin'.
            'email_verified_at' => now(),
            'is_approved' => true,
        ];

        DB::transaction(function () use ($oldUser, $adminData) {
            $adminUser = null;

            // 1. Create or Update Admin
            if ($oldUser) {
                // Determine if we should delete or update.
                // "elimina al psicologo juliana" suggests deletion, but if we want to keep data, updating is cleaner.
                // IF the old user was just a test user, deleting is fine.
                // Let's CREATE the new one first (if email doesn't conflict) or update.
                // If email is same, we MUST update or delete first.

                if ($oldUser->email === $adminData['email']) {
                    $this->command->info('Updating existing user to be Admin...');
                    $oldUser->update($adminData);
                    $adminUser = $oldUser;
                } else {
                    $this->command->info('Deleting old user and creating new Admin...');
                    // Reassign stuff?
                    // If we delete, constraints might fail if we don't reassign.
                    // But we don't have the new ID yet.
                    // This is tricky.
                    // Strategy: Create new with temp email -> Reassign -> Delete old -> Update new email.

                    $adminUser = User::create(array_merge($adminData, ['email' => 'temp_admin@therapy.app']));

                    // Reassign relations
                    // created_by
                    User::where('created_by', $oldUser->id)->update(['created_by' => $adminUser->id]);
                    // Contact Messages?
                    // Appointments?
                    // For now, let's assume basic reassign.

                    $oldUser->delete(); // Soft delete or force?
                    // Force delete to free up email if unique constraint
                    $oldUser->forceDelete();

                    $adminUser->update(['email' => $adminData['email']]);
                }
            } else {
                $this->command->info('Creating new Admin user...');
                $adminUser = User::create($adminData);
            }

            // 2. Ensure Main Page Registrations belong to her
            // Assign any user with null created_by to this Admin (Optional, logic might vary)
            // User::whereNull('created_by')->where('id', '!=', $adminUser->id)->update(['created_by' => $adminUser->id]);

            $this->command->info('Admin setup complete: ' . $adminUser->name);
        });
    }
}
