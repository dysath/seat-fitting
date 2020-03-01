<?php

namespace Denngarr\Seat\Fitting;

use App\Providers\AbstractSeatPlugin;

class FittingServiceProvider extends AbstractSeatPlugin
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

        // $this->mergeConfigFrom(
        //     __DIR__ . '/Config/fitting.permissions.php', 'web.permissions');
        
        $this->registerPermissions(
            __DIR__ . '/Config/Permissions/fitting.permissions.php', 'fitting');
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

    /**
     * Return the plugin public name as it should be displayed into settings.
     *
     * @example SeAT Web
     *
     * @return string
     */
    public function getName(): string
    {
        return 'SRP';
    }


    /**
     * Return the plugin repository address.
     *
     * @example https://github.com/eveseat/web
     *
     * @return string
     */
    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/dysath/seat-fitting';
    }

    /**
     * Return the plugin technical name as published on package manager.
     *
     * @example web
     *
     * @return string
     */
    public function getPackagistPackageName(): string
    {
        return 'seat-fitting';
    }

    /**
     * Return the plugin vendor tag as published on package manager.
     *
     * @example eveseat
     *
     * @return string
     */
    public function getPackagistVendorName(): string
    {
        return 'denngarr';
    }

    /**
     * Return the plugin installed version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return config('fitting.config.version');
    }
}
