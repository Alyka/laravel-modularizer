<?php

namespace Alyka\Modularizer\Console\Commands\Services;

abstract class SingleFileGenerator extends MakeServiceCommand
{
    /**
     * @inheritDoc
     */
    protected function getNameInput()
    {
        return $this->argument('name');
    }
}
