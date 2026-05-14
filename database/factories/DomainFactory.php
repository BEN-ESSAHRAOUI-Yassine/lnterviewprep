<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainFactory extends Factory
{
    protected $model = Domain::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'PHP OOP', 'Laravel ORM', 'MySQL', 'JavaScript ES6',
                'Vue.js', 'Docker', 'Git', 'Design Patterns',
                'SOLID Principles', 'Redis', 'API Design', 'Testing',
            ]),
            'color' => fake()->randomElement(['#3b82f6', '#22c55e', '#ef4444', '#a855f7', '#f97316', '#eab308', '#ec4899', '#6b7280']),
        ];
    }
}