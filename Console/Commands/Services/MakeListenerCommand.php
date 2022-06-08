<?php

namespace Alyka\Modularizer\Console\Commands\Services;

class MakeListenerCommand extends SingleFileGenerator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:listener {name} {service?} {dir?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate event listener class.';

    /**
     * Get the data for the generator.
     *
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'type' => 'Listener',
                'stub' => __DIR__ . '/stubs/Listeners/{{ serviceNameStudly }}Listener.stub',
            ],
       ];
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return parent::getDefaultNamespace($rootNamespace) . '\Listeners';
    }
}
