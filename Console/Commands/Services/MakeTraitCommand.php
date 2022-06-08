<?php

namespace Alyka\Modularizer\Console\Commands\Services;

class MakeTraitCommand extends SingleFileGenerator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lavamon-make:trait {name} {service?} {dir?}';

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
