<?php

namespace Database\Factories;

use App\Models\Concept;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConceptFactory extends Factory
{
    protected $model = Concept::class;

    public function definition(): array
    {
        return [
            'domain_id' => Domain::factory(),
            'title' => fake()->randomElement([
                'Le problème N+1', 'Service Container', 'Query Scopes',
                'Lazy vs Eager Loading', 'Polymorphic Relations',
                'Middleware', 'Events & Listeners', 'Service Providers',
                'Facades', 'Accessors & Mutators', 'Eloquent Collections',
                'Les différentes jointures SQL', 'Indexes et performances',
                'Transactions', 'ACID', 'Normalisation SQL',
                'Closure', 'Promises', 'Async/Await', 'Event Loop',
                'Prototype Chain', 'Hoisting', 'Closures vs Arrow Functions',
                'Docker Compose', 'Dockerfile multi-stage', 'Volumes',
                'Rebase vs Merge', 'Cherry-pick', 'Git Flow',
                'Singleton', 'Factory Pattern', 'Observers', 'Repository Pattern',
            ]),
            'explanation' => fake()->paragraphs(3, true),
            'difficulty' => fake()->randomElement(['junior', 'mid', 'senior']),
            'status' => fake()->randomElement(['to_review', 'in_progress', 'mastered']),
        ];
    }
}