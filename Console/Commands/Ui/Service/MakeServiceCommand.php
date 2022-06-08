<?php

namespace Alyka\Modularizer\Console\Commands\Ui\Service;

use Alyka\Modularizer\Foundation\Console\GeneratorCommand;

class MakeServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:ui-service
                            {service : The name of the service.}
                            {dir?  : The directory to store the service\'s files in, relative to service\'s root.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate ui service files.';

    /**
     * Get the da    ta for the generator.
     *
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'type' => 'component',
                'name' => 'components/{{ serviceNameStudly }}Form',
                'fileExtension' => 'vue',
                'stub' => __DIR__ . '/stubs/components/Form.stub',
            ],
            [
                'type' => 'components index',
                'name' => 'components/index',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/components/index.stub',
            ],
            [
                'type' => 'data',
                'name' => 'data/{{ serviceNameKebab }}',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/data/data.stub',
            ],
            [
                'type' => 'data index',
                'name' => 'data/index',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/data/index.stub',
            ],
            [
                'type' => 'type',
                'name' => 'types/{{ serviceNameKebab }}',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/types/type.stub',
            ],
            [
                'type' => 'types index',
                'name' => 'types/index',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/types/index.stub',
            ],
            [
                'type' => 'store',
                'name' => 'store',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/store.stub',
            ],
            [
                'type' => 'index',
                'name' => 'index',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/index.stub',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getPath($name)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $name)
        . '.'
        . $this->fileExtension;
    }

    /**
     * @inheritDoc
     */
    protected function rootNamespace()
    {
        return scriptName()
        . '\\frontend\\js\\services\\'
        . $this->getServiceName();
    }
}
