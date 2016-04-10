<?php

namespace MasterYuri\PubPreview;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Routing\Router;

use MasterYuri\PubPreview\Controller as Controller;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publishes(
        [
            __DIR__ . '/../config/pub-preview.php' => config_path('pub-preview.php'),
        ], 
        'config');

        $this->publishes(
        [
            __DIR__ . '/../public' => public_path(),
        ], 
        'public');
        
        $this->mergeConfigFrom(__DIR__ . '/../config/pub-preview.php', 'pub-preview');
        
        $this->setupRoutes($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        $router->group
        (
            [
                'middleware' => Controller::cfg('middleware'),
            ], 
            function ($router)
            {
                $router->get('/pub_preview/{width}/{height}/{mode}/{path}', 
                [
                    'as' => 'pub_preview_url', 
                    'uses' => Controller::class . '@getPreview'
                ]
                )->where('path', '.*');
            }
        );
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}