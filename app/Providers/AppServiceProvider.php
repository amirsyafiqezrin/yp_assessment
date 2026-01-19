<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('access-lecturer-portal', function (\App\Models\User $user) {
            return $user->isLecturer();
        });

        \Illuminate\Support\Facades\Gate::define('access-student-portal', function (\App\Models\User $user) {
            return $user->isStudent();
        });
    }
}
