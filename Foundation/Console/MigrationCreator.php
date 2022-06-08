<?php

namespace Core\Foundation\Console;

use Illuminate\Database\Migrations\MigrationCreator as MigrationsMigrationCreator;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator extends MigrationsMigrationCreator
{
    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @inheritDoc
     */
    public function stubPath()
    {
        return corePath('Console/Commands/Services/stubs/Database/Migrations');
    }
}
