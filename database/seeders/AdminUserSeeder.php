<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admincbo@kickpredict.com')],
            [
                'name' => 'Admin CBO',
                'email' => env('ADMIN_EMAIL', 'admincbo@kickpredict.com'),
                'password' => bcrypt(env('ADMIN_PASSWORD', 'KickPredict2025!Admin')),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
    }
} 