<?php

namespace Alyka\Modularizer\Console\Commands\Services\Payment;

use Alyka\Modularizer\Foundation\Console\GeneratorCommand;

class MakePaymentServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:payment-service
                            {service : The name of the service to generate.}
                            {dir?  : The directory to store this service in, relative to services root.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate payment service files.';

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
                'type' => 'Service contract',
                'name' => 'Contracts/{{ serviceNameStudly }}Service',
                'stub' => __DIR__ . '/stubs/Contracts/Service.stub',
            ],
            [
                'type' => 'Service facades',
                'name' => 'Facades/{{ serviceNameStudly }}Service',
                'stub' => __DIR__ . '/stubs/Facades/Service.stub',
            ],
            [
                'type' => 'Route facades',
                'name' => 'Facades/Route',
                'stub' => __DIR__ . '/stubs/Facades/Route.stub',
            ],
            [
                'type' => 'Controller',
                'name' => 'Http/Controllers/{{ serviceNameStudly }}Controller',
                'stub' => __DIR__ . '/stubs/Http/Controllers/Controller.stub',
            ],
            [
                'type' => 'Cancel request',
                'name' => 'Http/Requests/Cancel{{ serviceNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/CancelRequest.stub',
            ],
            [
                'type' => 'Settings request',
                'name' => 'Http/Requests/{{ serviceNameStudly }}SettingsRequest',
                'stub' => __DIR__ . '/stubs/Http/Requests/SettingsRequest.stub',
            ],
            [
                'type' => 'Verify request',
                'name' => 'Http/Requests/Verify{{ serviceNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/VerifyRequest.stub',
            ],
            [
                'type' => 'View-any request',
                'name' => 'Http/Requests/ViewAny{{ serviceNameStudly }}Request',
                'stub' => __DIR__ . '/stubs/Http/Requests/ViewAnyRequest.stub',
            ],
            [
                'type' => 'Setting Resource',
                'name' => 'Http/Resources/SettingResource',
                'stub' => __DIR__ . '/stubs/Http/Resources/SettingResource.stub',
            ],
            [
                'type' => 'App service provider',
                'name' => 'Providers/{{ serviceNameStudly }}ServiceProvider',
                'stub' => __DIR__ . '/stubs/Providers/ServiceProvider.stub',
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
