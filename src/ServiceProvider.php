<?php

namespace Spartaques\LaravelPinba;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/pinba.php';
        $this->mergeConfigFrom($configPath, 'pinba');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
         __DIR__ . '/../config/pinba.php' => config_path('pinba.php')
        ]);


        if (extension_loaded('pinba')) {

            if (ini_get ('pinba.enabled')) {

                pinba_hostname_set(config('app.name'));

                if(!empty(config('pinba.server_name'))) {
                    pinba_server_name_set(config('pinba.server_name'));
                }

                if(app()->runningInConsole()) {

                    $argv = $_SERVER['argv'];

                    $argc = $_SERVER['argc'];

                    pinba_schema_set('console');

                    $scriptName = '';

                    foreach ($argv as $arg) {
                        $scriptName .= $arg. ' ';
                    }
                } else {
                    pinba_schema_set('web');
                }

                /** @var \Illuminate\Routing\Router $router */
                $router = $this->app['router'];
                $router->middleware('pinba', Middleware::class);

                /** @var \Illuminate\Foundation\Http\Kernel $kernel */
                $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
                $kernel->pushMiddleware(Middleware::class);
            } else {
                ini_set('pinba.enabled', false);
            }
        }

        $this->app->singleton(LaravelPinba::class, function () {
            return new LaravelPinba();
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            LaravelPinba::class
        ];
    }
}
