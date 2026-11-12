<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Symfony\Component\Console\Formatter\OutputFormatter;

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

        $adminPassword = env('ADMIN_PASSWORD');

        if (! $adminPassword) {
            $adminPassword = Str::password(32);

            if (isset($this->command)) {
                $this->command->getOutput()->writeln(
                    'Mot de passe administrateur généré : '.OutputFormatter::escape($adminPassword),
                );
            }
        }

        User::create([
            'prenom' => 'Admin',
            'nom_famille' => 'AGAU',
            'email' => 'admin@agau.com',
            'telephone' => null,
            'pays' => null,
            'email_verified_at' => now(),
            'password' => $adminPassword,
            'newsletter' => false,
            'role' => 'admin',
        ]);
    }
}
