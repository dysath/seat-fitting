<?php

namespace Denngarr\Seat\Fitting;

use Illuminate\Support\ServiceProvider;

class FittingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //$this->addCommands();
        $this->add_routes();
        // $this->add_middleware($router);
        $this->add_views();
        $this->add_publications();
        $this->add_translations();
    }

    /**
     * Include the routes.
     */
    public function add_routes()
    {
        if (! $this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    public function add_translations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'fitting');
    }

    /**
     * Set the path and namespace for the views.
     */
    public function add_views()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'fitting');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/fitting.config.php', 'fitting.config');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/fitting.sidebar.php',
            'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/Config/fitting.permissions.php', 'web.permissions');
    }

    public function add_publications()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ]);
    }

    private function addCommands()
    {
    }
}
