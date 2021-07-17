<?php

namespace Jalno\LaravelMoney\Providers;

use Illuminate\Support\ServiceProvider;
use Jalno\LaravelMoney\CurrencyRepository;
use Jalno\Money\Contracts\ICurrencyRepository;

class CurrencyRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CurrencyRepository::class, CurrencyRepository::class);
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
