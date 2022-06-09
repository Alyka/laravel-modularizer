<?php

namespace Modularizer\Console\Commands\Modules;

class MakeTraitCommand extends SingleFileGenerator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:trait {name} {module?} {dir?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate trait.';

    /**
     * Get the data for the generator.
     *
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'type' => 'Trait',
                'stub' => __DIR__ . '/stubs/HasTrait.stub',
            ],
        ];
    }
}
