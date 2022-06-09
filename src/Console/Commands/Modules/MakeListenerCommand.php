<?php

namespace Modularizer\Console\Commands\Modules;

class MakeListenerCommand extends SingleFileGenerator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:listener {name} {module?} {dir?}';

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
                'stub' => __DIR__ . '/stubs/Listeners/{{ moduleNameStudly }}Listener.stub',
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
