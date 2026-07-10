<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call Roles & Permissions Seeder
        $this->call(RolesAndPermissionsSeeder::class);

        // Call Post Seeder
        $this->call(PostSeeder::class);

        // Seed a default test user if it doesn't exist
        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
