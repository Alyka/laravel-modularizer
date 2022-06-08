<?php

namespace Alyka\Modularizer\Console\Commands\Services;

class MakeEventCommand extends SingleFileGenerator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lavamon-make:event {name} {service?} {dir?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate event class.';

      /**
     * Get th    e data for the generator.
     *
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'type' => 'Event',
                'stub' => __DIR__ . '/stubs/Events/Event.stub',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return parent::getDefaultNamespace($rootNamespace) . '\Events';
    }
}
