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
            }

            if (config('pinba.enabled')) {

                    ini_set('pinba.enabled', config('pinba.enabled'));

                if (ini_get('pinba.server') === '') {
                    ini_set('pinba.server', config('pinba.server'));
                }

                if(app()->runningInConsole()) {

                    $argv = $_SERVER['argv'];

                    $argc = $_SERVER['argc'];

                    pinba_schema_set('console');
                    if($argc > 2) {
                    pinba_script_name_set($argv[0] . ' '.$argv[1]);
                    } else {
                        pinba_script_name_set($argv[1]);
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
