<?php

namespace Modularizer\Console\Commands\Ui\Service\Payment;

use Modularizer\Foundation\Console\GeneratorCommand;
use Modularizer\Support\ServiceProvider;

class MakePaymentServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:ui-payment-module
                            {module : The name of the module.}
                            {dir?  : The directory to store the module\'s files in, relative to module\'s root.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate ui module files.';

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
                'name' => 'components/{{ moduleNameStudly }}Button',
                'fileExtension' => 'vue',
                'stub' => __DIR__ . '/stubs/components/Button.stub',
            ],
            [
                'type' => 'component',
                'name' => 'components/{{ moduleNameStudly }}SettingForm',
                'fileExtension' => 'vue',
                'stub' => __DIR__ . '/stubs/components/SettingForm.stub',
            ],
            [
                'type' => 'components index',
                'name' => 'components/index',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/components/index.stub',
            ],
            [
                'type' => 'data',
                'name' => 'data/setting',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/data/setting.stub',
            ],
            [
                'type' => 'data index',
                'name' => 'data/index',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/data/index.stub',
            ],
            [
                'type' => 'type',
                'name' => 'types/setting',
                'fileExtension' => 'ts',
                'stub' => __DIR__ . '/stubs/types/setting.stub',
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
        return trim(config(ServiceProvider::NAME.'.frontend_path'), '/\\')
        . DIRECTORY_SEPARATOR
        . $this->getModuleName();
    }
}
