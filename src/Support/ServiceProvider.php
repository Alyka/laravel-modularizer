<?php

namespace Modularizer\Support;

use Modularizer\Console\Kernel;
use Modularizer\Foundation\Providers\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @var string
     */
    public const NAME = 'modularizer';

    /**
     * @inheritdoc
     */
    protected $consoleKernel = Kernel::class;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $this->publishConfig();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        parent::register();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $source = __DIR__ . '/../../config/modularizer.php';

        $this->mergeConfigFrom($source, self::NAME);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function publishConfig()
    {
        $source = __DIR__ . '/../../config/modularizer.php';

        $this->publishes([$source => config_path(self::NAME.'.php')], 'config');
    }
}
