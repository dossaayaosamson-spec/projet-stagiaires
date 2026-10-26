<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSuspension;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSuspension>
 */
class UserSuspensionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'admin_id' => User::factory()->create(['role' => 'admin'])->id,
            'motif' => $this->faker->sentence(),
            'suspended_at' => now(),
        ];
    }
}
