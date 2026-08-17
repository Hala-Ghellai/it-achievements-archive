<?php

namespace App\Providers;

use App\Models\Achievement;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
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
        Carbon::setLocale(config('app.locale', 'ar'));

        // Admins may add achievements to any department; staff only to their own.
        Gate::define('create-achievements', function (User $user) {
            return $user->isAdmin() || $user->department_id !== null;
        });

        Gate::define('create-achievement-in', function (User $user, Department $department) {
            return $user->isAdmin() || $user->department_id === $department->id;
        });

        Gate::define('manage-achievement', function (User $user, Achievement $achievement) {
            return $user->isAdmin() || $user->department_id === $achievement->department_id;
        });
    }
}
