<?php

namespace Towoju5\AdminUserChat;

use Illuminate\Support\ServiceProvider;

class AdminUserChatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'admin-user-chat');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin-user-chat');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/admin_user_chat.php' => config_path('admin-user-chat.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/database/migrations/' => database_path('/migrations')
            ], 'migrations');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/admin-user-chat'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/admin-user-chat'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/admin-user-chat'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->publishes([__DIR__.'/config/admin_user_chat.php' => config_path('admin_user_chat.php')]);
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        // Register the main class to use with the facade
        $this->app->singleton('woju-chat', function () {
            return new AdminUserChat;
        });
    }
}
