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
        $this->app->bind(
            \App\Repositories\Interfaces\BorrowerRepositoryInterface::class,
            \App\Repositories\Eloquent\BorrowerRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\LoanRepositoryInterface::class,
            \App\Repositories\Eloquent\LoanRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\PaymentRepositoryInterface::class,
            \App\Repositories\Eloquent\PaymentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
