<?php

namespace Alyka\Modularizer\Console\Commands\Services;

use Alyka\Modularizer\Foundation\Console\GeneratorCommand;

class MakeRouterCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lavamon-make:router
                            {service : The name of the service to generate.}
                            {dir?  : The directory to store the files in, relative to services root.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate router file and its facade.';

    /**
     * Get the da    ta for the generator.
     *
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'type' => 'Router',
                'name' => 'Router',
                'stub' => __DIR__ . '/stubs/Router.stub',
            ],
            [
                'type' => 'Router facade',
                'name' => 'Facades/Route',
                'stub' => __DIR__ . '/stubs/Facades/Route.stub',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\\".$this->getDirectory();
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return serviceNamespace();
    }
}
