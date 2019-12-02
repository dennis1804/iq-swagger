<?php

namespace ReinderEU\IqSwagger;

use Illuminate\Support\ServiceProvider;

class SwaggerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
   protected $commands = [
        ApiDocMaker::class
    ];

    public function register() { 


        $this->commands($this->commands);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
            

        $this->loadRoutesFrom(__DIR__.'/routes.php');


        $this->loadViewsFrom(__DIR__.'/views', 'swagger');

        // Publish config.
        $this->publishes([
            __DIR__.'/../config/iq_swagger.php' => config_path('iq_swagger.php'),
        ], 'config');



        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/swagger'),
        ], 'public');


    }


    public function provides()
    {
        return ['iq-swagger'];
    }
}