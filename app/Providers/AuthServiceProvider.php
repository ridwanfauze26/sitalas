<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin', function($user){
            return count(array_intersect(["admin"], explode(" ",$user->role)));
        });

        Gate::define('kepala', function($user){
            return count(array_intersect(["kepala"], explode(" ",$user->role)));
        });

        Gate::define('verifikator', function($user){
            return count(array_intersect(["verifikator"], explode(" ",$user->role)));
        });

        Gate::define('pegawai', function($user){
            return count(array_intersect(["pegawai"], explode(" ",$user->role)));
        });
    }
}
