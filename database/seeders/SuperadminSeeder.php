<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if there are any existing admins
        if (Admin::count() === 0) {
            // Create the superadmin
            Admin::create([
                'name' => 'admineido',
                'email' => 'admineido@gmail.com', // Change this to your desired email
                'password' => Hash::make('password'), // Change this to your desired password
                'role' => 'superadmin',
            ]);

            $this->command->info('Superadmin account has been created.');
        } else {
            $this->command->info('Admins already exist. No superadmin created.');
        }
    }
}
