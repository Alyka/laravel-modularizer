<?php

namespace Alyka\Modularizer\Console\Commands\Services;

use Alyka\Modularizer\Foundation\Console\GeneratorCommand;

class MakeServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lavamon-make:service
                            {service : The name of the service to generate.}
                            {dir?  : The directory to store this service in, relative to services root.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate service files.';

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
                'name' => 'Contracts/{{ serviceNameStudly }}Repository',
                'stub' => __DIR__ . '/stubs/Contracts/Repository.stub',
            ],
            [
                'type' => 'Service contract',
                'name' => 'Contracts/{{ serviceNameStudly }}Service',
                'stub' => __DIR__ . '/stubs/Contracts/Service.stub',
            ],
            [
                'type' => 'Factory',
                'name' => 'Database/Factories/{{ serviceNameStudly }}Factory',
                'stub' => __DIR__ . '/stubs/Database/Factories/Factory.stub',
            ],
            [
                'type' => 'Create table migration',
                'name' => 'Database/Migrations/{{ migrationTimestamp }}_create_{{ serviceNamePluralSnake }}_table',
                'stub' => __DIR__ . '/stubs/Database/Migrations/create_table.stub',
            ],
            [
                'type' => 'Seeder',
                'name' => 'Database/Seeders/{{ serviceNameStudly }}TableSeeder',
                'stub' => __DIR__ . '/stubs/Database/Seeders/TableSeeder.stub',
            ],
            [
                'type' => 'Repository facade',
                'name' => 'Facades/{{ serviceNameStudly }}Repository',
                'stub' => __DIR__ . '/stubs/Facades/Repository.stub',
            ],
            [
                'type' => 'Service facade',
                'name' => 'Facades/{{ serviceNameStudly }}Service',
                'stub' => __DIR__ . '/stubs/Facades/Service.stub',
            ],
            [
                'type' => 'Route facade',
                'name' => 'Facades/Route',
                'stub' => __DIR__ . '/stubs/Facades/Route.stub',
            ],
            [
                'type' => 'Controller',
                'name' => 'Http/Controllers/{{ serviceNameStudly }}Controller',
                'stub' => __DIR__ . '/stubs/Http/Controllers/Controller.stub',
            ],
            [
                'type' => 'Create request',
                'name' => 'Http/Requests/Create{{ serviceNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/CreateRequest.stub',
            ],
            [
                'type' => 'Delete request',
                'name' => 'Http/Requests/Delete{{ serviceNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/DeleteRequest.stub',
            ],
            [
                'type' => 'Update request',
                'name' => 'Http/Requests/Update{{ serviceNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/UpdateRequest.stub',
            ],
            [
                'type' => 'View any request',
                'name' => 'Http/Requests/ViewAny{{ serviceNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/ViewAnyRequest.stub',
            ],
            [
                'type' => 'View one request',
                'name' => 'Http/Requests/View{{ serviceNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/ViewRequest.stub',
            ],
            [
                'type' => 'Api resource',
                'name' => 'Http/Resources/{{ serviceNameStudly }}Resource',
                'stub' => __DIR__ . '/stubs/Http/Resources/Resource.stub',
            ],
            [
                'type' => 'Model',
                'name' => 'Models/{{ serviceNameStudly }}',
                'stub' => __DIR__ . '/stubs/Models/Model.stub',
            ],
            [
                'type' => 'Policy',
                'name' => 'Policies/{{ serviceNameStudly }}Policy',
                'stub' => __DIR__ . '/stubs/Policies/Policy.stub',
            ],
            [
                'type' => 'Auth service provider',
                'name' => 'Providers/AuthServiceProvider',
                'stub' => __DIR__ . '/stubs/Providers/AuthServiceProvider.stub',
            ],
            [
                'type' => 'App service provider',
                'name' => 'Providers/{{ serviceNameStudly }}ServiceProvider',
                'stub' => __DIR__ . '/stubs/Providers/ServiceProvider.stub',
            ],
            [
                'type' => 'Repository',
                'name' => 'Repositories/{{ serviceNameStudly }}RepositoryEloquent',
                'stub' => __DIR__ . '/stubs/Repositories/RepositoryEloquent.stub',
            ],
            [
                'type' => 'Service',
                'name' => 'Services/{{ serviceNameStudly }}Service',
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
        return serviceNamespace();
    }
}
