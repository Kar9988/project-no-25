<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Admin\GenresController;
use App\Policies\GenresPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        GenresController::class =>GenresPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
