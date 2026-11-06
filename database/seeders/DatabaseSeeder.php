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
        $this->call([
            AuthorSeeder::class,
            BookSeeder::class,
            BookTestSeeder::class,
            ShortStorySeeder::class,
            SubscriptionPlanSeeder::class,
        ]);

        User::factory()->create([
            'prenom' => 'Admin',
            'nom_famille' => 'AGAU',
            'email' => 'admin@agau.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
