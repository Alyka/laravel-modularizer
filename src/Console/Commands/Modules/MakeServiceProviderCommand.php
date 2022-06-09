<?php

namespace Modularizer\Console\Commands\Modules;

class MakeServiceProviderCommand extends MakeModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:service-provider
                            {module}
                            {dir?}
                            {--a|auth}
                            {--e|event}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate  provider class.';

    /**
     * Get the data     for the generator.
     *
     * @return array
     */
    protected function getData()
    {
        if($this->option('event'))
        {
            return [
                [
                    'type' => 'Event service provider',
                    'name' => 'Providers/EventServiceProvider',
                    'stub' => __DIR__ . '/stubs/Providers/EventServiceProvider.stub',
                ],
            ];
        }
        elseif($this->option('auth'))
        {
            return [
                [
                    'type' => 'Auth service provider',
                    'name' => 'Providers/AuthServiceProvider',
                    'stub' => __DIR__ . '/stubs/Providers/AuthServiceProvider.stub',
                ],
            ];
        }
        else
        {
            return [
                [
                    'type' => 'App service provider',
                    'name' => 'Providers/{{ moduleNameStudly }}ServiceProvider',
                    'stub' => __DIR__ . '/stubs/Providers/ServiceProvider.stub',
                ],
            ];
        }
    }
}
