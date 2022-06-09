<?php

namespace Modularizer\Console\Commands\Modules;

abstract class SingleFileGenerator extends MakeModuleCommand
{
    /**
     * @inheritDoc
     */
    protected function getNameInput()
    {
        return $this->argument('name');
    }
}
