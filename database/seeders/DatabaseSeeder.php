<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed RISTCON 2026 test data
        $this->call([
            Ristcon2026Seeder::class,
        ]);

        // Create admin user
        User::factory()->create([
            'name' => 'RISTCON Admin',
            'email' => 'admin@ristcon.ruh.ac.lk',
        ]);
    }
}
