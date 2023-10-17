<?php

namespace gtiger117\athlo;

use Illuminate\Support\ServiceProvider;

class athloProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Include the package classmap autoloader
        if (\File::exists(__DIR__.'/../vendor/autoload.php'))
        {
            include __DIR__.'/../vendor/autoload.php';
        }

        /**
        * Routes
        */
        
        // Method 1
        // A simple include, but in the routes files, controllers should be called by their namespace
        // include __DIR__.'/routes/web.php';
        
        // Method 2
        // A Better method, extend the app routes by adding a group with a specified namespace

        $this->app->router->group(['namespace' => 'gtiger117\athlo\App\Http\Controllers'],
            function(){
                require __DIR__.'/routes/web.php';
            }
        );

        /**
        * Views
        * use: view('PackageName::view_name');
        */
        $this->loadViewsFrom(__DIR__.'/resources/views', 'gtiger117\athlo');

        /*
        * php artisan vendor:publish
        * Existing files will not be published
        */

        // Publish views to resources/views/vendor/vendor-name/package-name
        $this->publishes(
            [
                __DIR__.'/resources/views' => base_path('resources/views/vendor/gtiger117/athlo'),
            ]
        );

        // Publish assets to public/vendor/vendor-name/package-name
        $this->publishes([
            __DIR__.'/public' => public_path('vendor/gtiger117/athlo'),
        ], 'public');

        // Publish configurations to config/vendor/vendor-name/package-name
        // Config::get('vendor.gtiger117.athlo')
        $this->publishes([
            __DIR__.'/config' => config_path('vendor/gtiger117/athlo'),
        ]);

        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware('gtiger117\athlo\App\Http\Middleware\MiddlewareExample');

        /**
         * Register migrations, so they will be automatically run when the php artisan migrate command is executed.
         */
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        /**
         * Register commands, so you may execute them using the Artisan CLI.
         */
        if ($this->app->runningInConsole()) {
            $this->commands([
                \gtiger117\athlo\App\Console\Commands\Hello::class,
            ]);
        }

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        /**
        * Merge configurations
        * Config::get('packages.gtiger117.athlo')
        */
        $this->mergeConfigFrom(
            __DIR__.'/config/app.php', 'packages.gtiger117.athlo.app'
        );

        $this->app->bind('ClassExample', function(){
            return $this->app->make('gtiger117\athlo\Classes\ClassExample');
        });

    }
}
