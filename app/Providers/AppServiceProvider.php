<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\{User, Complaint};

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $view->with('pendingCraftsmenCount', User::where('role', 'craftsman')
                ->whereHas('craftsmanProfile', fn($q) => $q->where('is_approved', 0))
                ->count());
            $view->with('pendingComplaintsCount', Complaint::where('status', 'pending')->count());
        });
    }
}
