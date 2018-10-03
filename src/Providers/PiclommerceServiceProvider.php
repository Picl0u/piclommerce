<?php

namespace Piclou\Piclommerce\Providers;

use Illuminate\Support\ServiceProvider;
use Piclou\Piclommerce\Http\Console\Commands\InstallPiclommerceCommand;
use Piclou\Piclommerce\Http\Console\Commands\UnistallPiclommerceCommande;
use Piclou\Piclommerce\Http\Middleware\AdministrationAccess;
use Piclou\Piclommerce\Http\Middleware\LangMiddleware;
use Piclou\Piclommerce\Http\Middleware\OrderAccessMiddleware;
use Piclou\Piclommerce\Http\Middleware\UserAccountMiddleware;

class PiclommerceServiceProvider extends ServiceProvider
{

    /* Controller's namespace */
    private $controllerNamespace = 'Piclou\Piclommerce\Http\Controllers\\';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mapConfig();
        $this->mapRoutes();
        $this->mapViews();
        $this->mapPublishes();
        $this->loadMigrations();
        $this->loadTranslations();
        $this->registerMiddleware();
        $this->mapControllers();
        $this->mapCommands();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    public function mapCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallPiclommerceCommand::class,
                UnistallPiclommerceCommande::class,
            ]);
        }
    }

    /**
     * Get config mapped
     *
     * @return void
     */
    protected function mapConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/piclommerce.php', 'piclommerce'
        );
    }

    /**
     * Get some routes mapped
     *
     * @return void
     */
    protected function mapRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');
    }

    /**
     * Get controllers mapped
     *
     * @return void
     */
    protected function mapControllers()
    {
        /* Auth */
        $this->app->make($this->controllerNamespace . 'Auth\ForgotPasswordController');
        $this->app->make($this->controllerNamespace . 'Auth\LoginController');
        $this->app->make($this->controllerNamespace . 'Auth\RegisterController');
        $this->app->make($this->controllerNamespace . 'Auth\ResetPasswordController');
    }

    /**
     * Get views mapped
     *
     * @return void
     */
    protected function mapViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'piclommerce');
    }

    /**
     * Get publishes mapped
     *
     * @return void
     */
    protected function mapPublishes()
    {
        $this->publishes([
            __DIR__ . '/../../resources' => base_path('resources'),
            __DIR__ . '/../../config' => config_path('piclommerce'),
        ]);
    }

    /**
     * Load migrations
     *
     * @return void
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }


    /**
     * Load translations
     *
     * @return void
     */
    protected function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'piclommerce');
    }

    /**
     * Register our middleware
     *
     * @return void
     */
    protected function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('piclommerce.lang', LangMiddleware::class );
        $this->app['router']->aliasMiddleware('piclommerce.admin', AdministrationAccess::class);
        $this->app['router']->aliasMiddleware('piclommerce.cart', OrderAccessMiddleware::class);
        $this->app['router']->aliasMiddleware('piclommerce.user', UserAccountMiddleware::class);
    }

}
