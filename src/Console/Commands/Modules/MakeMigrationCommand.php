<?php

namespace Modularizer\Console\Commands\Modules;

use Modularizer\Foundation\Console\MigrationCreator;
use Illuminate\Support\Composer;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;

class MakeMigrationCommand extends MigrateMakeCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'modularizer-make:migration
        {name : The name of the migration}
        {module : The name of the module this migration belongs to}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate module migration file.';

    /**
     * Create a new migration install command instance.
     *
     * @param  MigrationCreator  $creator
     * @param  Composer  $composer
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);

        $this->creator = $creator;
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return namespacePath(moduleNamespace())
        . $this->argument('module').'/Database/Migrations';
    }
}
