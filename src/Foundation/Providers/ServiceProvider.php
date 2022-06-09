<?php

namespace Modularizer\Foundation\Providers;

use Modularizer\Support\Cast;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider as SupportServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Routing\Router;

abstract class ServiceProvider extends SupportServiceProvider
{
    /**
     * Service provider classes to register.
     *
     * @var string[]
     */
    protected $providers = [];

    /**
     * The console kernel class of the service provider.
     *
     * @var \Modularizer\Foundation\Http\Kernel
     */
    protected $consoleKernel;

    /**
     * The http kernel class of the service provider.
     *
     * @var \Modularizer\Foundation\Http\Kernel
     */
    protected $httpKernel;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->registerKernels();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerProviders();

        $this->app->bind('cast', function () {
            return new Cast();
        });
    }

    /**
     * Register all specified service providers.
     *
     * @return void
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Register the application's kernels.
     *
     * @return void
     */
    protected function registerKernels()
    {
        $this->registerHttpKernel();
        $this->registerConsoleKernel();
    }

    /**
     * Register service's console kernel.
     *
     * @return void
     */
    protected function registerConsoleKernel()
    {
        if (! $this->consoleKernel) {
            return;
        }

        if (! $this->app->runningInConsole()) {
            return;
        }

        (new $this->consoleKernel($this->app, $this->app[Dispatcher::class]))
            ->bootstrap();
    }

    /**
     * Register service's http kernel.
     *
     * @return void
     */
    protected function registerHttpKernel()
    {
        if (! $this->httpKernel) {
            return;
        }

        if ($this->app->runningInConsole()) {
            return;
        }

        (new $this->httpKernel($this->app, $this->app[Router::class]))->bootstrap();
    }

    /**
     * Recursively and distinctively merge into the existing auth configuration
     *
     * The merged config will take precedence over existing config.
     *
     * @param string $path
     * @param string $key
     * @return void
     */
    protected function mergeConfigFromReverse($path, $key)
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            $defaultConfig = $config->get($key, []);
            $customConfig = require $path;

            $config->set($key, array_merge_recursive_distinct(
                $defaultConfig,
                $customConfig
            ));
        }
    }
}
