<?php

namespace Database\Seeders;

use App\Models\Concept;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Yassine',
            'email' => 'yassine@example.com',
        ]);

        Domain::factory(6)
            ->for($user)
            ->has(Concept::factory(4))
            ->create();
    }
}