<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create initial admin user
        User::create([
            'id' => (string) Str::uuid(),
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin123!@#'),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $this->command->info('Initial admin user created:');
        $this->command->info('Username: admin');
        $this->command->info('Password: Admin123!@#');
        $this->command->warn('Please change the password after first login!');
    }
}
