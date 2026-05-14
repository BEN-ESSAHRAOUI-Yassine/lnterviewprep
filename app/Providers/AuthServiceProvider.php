<?php

namespace App\Providers;

use App\Models\Concept;
use App\Models\Domain;
use App\Models\GeneratedQuestion;
use App\Policies\ConceptPolicy;
use App\Policies\DomainPolicy;
use App\Policies\GeneratedQuestionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Domain::class => DomainPolicy::class,
        Concept::class => ConceptPolicy::class,
        GeneratedQuestion::class => GeneratedQuestionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}