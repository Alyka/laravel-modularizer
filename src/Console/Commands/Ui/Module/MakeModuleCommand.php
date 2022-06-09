<?php

namespace Modularizer\Console\Commands\Ui\Module;

use Modularizer\Foundation\Console\GeneratorCommand;
use Modularizer\Support\ServiceProvider;

class MakeModuleCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:ui-module
                            {module : The name of the module.}
                            {app :  The name of the app this module belongs to.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate ui module for a given app.';

    /**
     * Get the da    ta for the generator.
     *
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'type' => 'page',
                'name' => 'pages/Index',
                'fileExtension' => 'vue',
                'stub' => __DIR__ . '/stubs/pages/Index.stub',
            ],
            [
                'type' => 'components',
                'name' => 'components',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/components.stub',
            ],
            [
                'type' => 'index',
                'name' => 'index',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/index.stub',
            ],
            [
                'type' => 'route',
                'name' => 'routes',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/routes.stub',
            ],
            [
                'type' => 'sidebar',
                'name' => 'sidebar',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/sidebar.stub',
            ],
        ];
    }

    /**
     * Get the name of the module this file belongs to
     *
     * @return string
     */
    protected function getModuleName()
    {
        return $this->argument('module');
    }

    /**
     * Get the directory this file will be written to,
     * relative to the module root folder.
     *
     * @return string
     */
    protected function getDirectory()
    {
          return $this->argument('app');
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $name)
        . '.'
        . $this->fileExtension;
    }

    /**
     * @inheritDoc
     */
    protected function rootNamespace()
    {
        return trim(config(ServiceProvider::NAME.'.frontend_path'), '\\')
        . '\\'
        . $this->getDirectory()
        . '\\'
        . $this->getModuleName();
    }
}
