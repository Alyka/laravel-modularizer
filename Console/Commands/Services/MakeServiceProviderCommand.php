<?php

namespace Alyka\Modularizer\Console\Commands\Services;

class MakeServiceProviderCommand extends MakeServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lavamon-make:service-provider
                            {service}
                            {dir?}
                            {--a|auth}
                            {--e|event}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate service provider class.';

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
                    'name' => 'Providers/{{ serviceNameStudly }}ServiceProvider',
                    'stub' => __DIR__ . '/stubs/Providers/ServiceProvider.stub',
                ],
            ];
        }
    }
}
