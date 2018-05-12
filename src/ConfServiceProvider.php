<?php

namespace Ants\Conf;

use Illuminate\Support\ServiceProvider;

class ConfServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Conf',function (){
            return new Conf();
        });
    }
}
