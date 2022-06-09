<?php

namespace Modularizer\Console\Commands\Modules;

use Modularizer\Foundation\Console\GeneratorCommand;

class MakeModuleCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:module
                            {module : The name of the module to generate.}
                            {dir?  : The directory to store this module in, relative to services root.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate module files.';

    /**
     * Get the da    ta for the generator.
     *
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'type' => 'Config',
                'name' => 'config/config',
                'stub' => __DIR__ . '/stubs/config/config.stub',
            ],
            [
                'type' => 'Repository contract',
                'name' => 'Contracts/{{ moduleNameStudly }}Repository',
                'stub' => __DIR__ . '/stubs/Contracts/Repository.stub',
            ],
            [
                'type' => 'Service contract',
                'name' => 'Contracts/{{ moduleNameStudly }}Service',
                'stub' => __DIR__ . '/stubs/Contracts/Service.stub',
            ],
            [
                'type' => 'Factory',
                'name' => 'Database/Factories/{{ moduleNameStudly }}Factory',
                'stub' => __DIR__ . '/stubs/Database/Factories/Factory.stub',
            ],
            [
                'type' => 'Create table migration',
                'name' => 'Database/Migrations/{{ migrationTimestamp }}_create_{{ moduleNamePluralSnake }}_table',
                'stub' => __DIR__ . '/stubs/Database/Migrations/create_table.stub',
            ],
            [
                'type' => 'Seeder',
                'name' => 'Database/Seeders/{{ moduleNameStudly }}TableSeeder',
                'stub' => __DIR__ . '/stubs/Database/Seeders/TableSeeder.stub',
            ],
            [
                'type' => 'Repository facade',
                'name' => 'Facades/{{ moduleNameStudly }}Repository',
                'stub' => __DIR__ . '/stubs/Facades/Repository.stub',
            ],
            [
                'type' => 'Service facade',
                'name' => 'Facades/{{ moduleNameStudly }}Service',
                'stub' => __DIR__ . '/stubs/Facades/Service.stub',
            ],
            [
                'type' => 'Route facade',
                'name' => 'Facades/Route',
                'stub' => __DIR__ . '/stubs/Facades/Route.stub',
            ],
            [
                'type' => 'Controller',
                'name' => 'Http/Controllers/{{ moduleNameStudly }}Controller',
                'stub' => __DIR__ . '/stubs/Http/Controllers/Controller.stub',
            ],
            [
                'type' => 'Create request',
                'name' => 'Http/Requests/Create{{ moduleNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/CreateRequest.stub',
            ],
            [
                'type' => 'Delete request',
                'name' => 'Http/Requests/Delete{{ moduleNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/DeleteRequest.stub',
            ],
            [
                'type' => 'Update request',
                'name' => 'Http/Requests/Update{{ moduleNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/UpdateRequest.stub',
            ],
            [
                'type' => 'View any request',
                'name' => 'Http/Requests/ViewAny{{ moduleNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/ViewAnyRequest.stub',
            ],
            [
                'type' => 'View one request',
                'name' => 'Http/Requests/View{{ moduleNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/ViewRequest.stub',
            ],
            [
                'type' => 'Api resource',
                'name' => 'Http/Resources/{{ moduleNameStudly }}Resource',
                'stub' => __DIR__ . '/stubs/Http/Resources/Resource.stub',
            ],
            [
                'type' => 'Model',
                'name' => 'Models/{{ moduleNameStudly }}',
                'stub' => __DIR__ . '/stubs/Models/Model.stub',
            ],
            [
                'type' => 'Policy',
                'name' => 'Policies/{{ moduleNameStudly }}Policy',
                'stub' => __DIR__ . '/stubs/Policies/Policy.stub',
            ],
            [
                'type' => 'Auth service provider',
                'name' => 'Providers/AuthServiceProvider',
                'stub' => __DIR__ . '/stubs/Providers/AuthServiceProvider.stub',
            ],
            [
                'type' => 'App service provider',
                'name' => 'Providers/{{ moduleNameStudly }}ServiceProvider',
                'stub' => __DIR__ . '/stubs/Providers/ServiceProvider.stub',
            ],
            [
                'type' => 'Repository',
                'name' => 'Repositories/{{ moduleNameStudly }}RepositoryEloquent',
                'stub' => __DIR__ . '/stubs/Repositories/RepositoryEloquent.stub',
            ],
            [
                'type' => 'Service',
                'name' => 'Services/{{ moduleNameStudly }}Service',
                'stub' => __DIR__ . '/stubs/Services/Service.stub',
            ],
            [
                'type' => 'Router',
                'name' => 'Router',
                'stub' => __DIR__ . '/stubs/Router.stub',
            ],
            [
                'type' => 'composer',
                'name' => 'composer',
                'fileExtension' => 'json',
                'stub' => __DIR__ . '/stubs/composer.stub',
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
        return moduleNamespace();
    }
}
