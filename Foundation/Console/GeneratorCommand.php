<?php

namespace Core\Foundation\Console;

use Illuminate\Support\Str;
use Illuminate\Support\Composer;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand as BaseGeneratorCommand;

abstract class GeneratorCommand extends BaseGeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type;

    /**
     * The root namespace in use.
     *
     * @var string
     */
    protected $rootNamespace;

    /**
     * The stub to be used by the generator.
     *
     * @var string
     */
    protected $stub;

    /**
     * The class name of the file to be generated.
     *
     * @var string
     */
    protected $fileName;

    /**
     * The file extension.
     *
     * @var string
     */
    protected $fileExtension;

    /**
     * The Composer instance.
     *
     * @var Composer
     */
    protected $composer;

    /**
     * Create new instance of the generator command.
     *
     * @param Composer $composer
     * @param Filesystem $files
     * @return void
     */
    public function __construct(Composer $composer, Filesystem $files)
    {
        parent::__construct($files);

        $this->composer = $composer;
    }

    /**
     * @inheritDoc
     */
    public function handle()
    {
        $data = $this->getData();

        foreach ($data as $fileInfo) {
            $this->setFileName(@$fileInfo['name']);
            $this->setType(@$fileInfo['type']);
            $this->setFileExtension(@$fileInfo['fileExtension']);
            $this->setStub($fileInfo['stub']);

            parent::handle();
        }
    }

    /**
     * Set the filename
     *
     * @param string|null $fileName
     * @return void
     */
    protected function setFilename($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Set the file type.
     *
     * @param string|null $type
     * @return void
     */
    protected function setType($type)
    {
        $this->type = $this->qualifyClass($this->getNameInput());
    }

    /**
     * Set the file extension.
     *
     * @param string|null $fileExtension
     * @return void
     */
    protected function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension ?? $this->defaultFileExtension();
    }

    /**
     * Set the stub.
     *
     * @param string $stub
     * @return void
     */
    protected function setStub(string $stub)
    {
        $this->stub = $stub;
    }

    /**
     * Get the data for the generator.
     *
     * @return array
     */
    abstract protected function getData();

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->replacePlaceholders($this->stub, true);
    }

    /**
     * Get the desired file name from user input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return $this->replacePlaceholders($this->fileName);
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * @inheritDoc
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        return $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name;
    }

    /**
     * Get the destination file path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $rootNamespace = $this->rootNamespace();
        $nameWithoutNamespace = Str::after($name, $rootNamespace);

        return namespacePath($rootNamespace)
        . DIRECTORY_SEPARATOR
        . str_replace('\\', DIRECTORY_SEPARATOR, $nameWithoutNamespace)
        . '.'
        . $this->fileExtension;
    }

    /**
     * Get the name of the service this file belongs to.
     *
     * @return string
     */
    protected function getServiceName()
    {
        if ($serviceName = $this->argument('service')) {
            return $serviceName;
        }

        $name = $this->qualifyClass($this->getNameInput());

        return Str::before(Str::after($name, $this->rootNamespace()), '\\');
    }

    /**
     * Get the directory this file will be written to,
     * relative to the services root folder.
     *
     * @return string
     */
    protected function getDirectory()
    {
        return $this->argument('dir') ?? $this->argument('service');
    }

    /**
     * Get generated file extension.
     *
     * @return string
     */
    protected function defaultFileExtension()
    {
        return 'php';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = $this->replacePlaceholders($stub);

        return parent::replaceNamespace($stub, $name);
    }

    /**
     * Replace all placeholders in the given string with
     * corresponding values.
     *
     * @param string $string
     * @param bool $stripe Remove the placeholders
     * completely instead of replacing them.
     * @return string
     */
    protected function replacePlaceholders($string, $stripe = false)
    {
        $serviceName = $this->getServiceName();
        $serviceNamespace = serviceNamespace();
        $serviceDirectory = $this->getDirectory();
        $serviceNameStudly = Str::studly($serviceName);
        $serviceNameCamel = Str::camel($serviceName);
        $serviceNameSnake = Str::snake($serviceNameStudly);
        $serviceNameKebab = Str::kebab($serviceName);
        $serviceNameWords = str_replace('_', ' ', $serviceNameSnake);
        $serviceNamePlural = Pluralizer::plural($serviceName);
        $serviceNamePluralSnake = Str::snake($serviceNamePlural);
        $serviceNamePluralCamel = Str::camel($serviceNamePlural);
        $serviceNamePluralKebab = Str::kebab($serviceNamePlural);
        $serviceNamePluralWords = str_replace('_', ' ', $serviceNamePluralSnake);
        $migrationTimestamp = $this->getDatePrefix();

        return str_replace(
            [
                '{{ serviceNamespace }}',
                '{{ serviceDirectory }}',
                '{{ serviceNameStudly }}',
                '{{ serviceNameCamel }}',
                '{{ serviceNameSnake }}',
                '{{ serviceNameKebab }}',
                '{{ serviceNameWords }}',
                '{{ serviceNamePlural }}',
                '{{ serviceNamePluralSnake }}',
                '{{ serviceNamePluralCamel }}',
                '{{ serviceNamePluralKebab }}',
                '{{ serviceNamePluralWords }}',
                '{{ migrationTimestamp }}',
            ],
            [
                $stripe ? '' : $serviceNamespace,
                $stripe ? '' : $serviceDirectory,
                $stripe ? '' : $serviceNameStudly,
                $stripe ? '' : $serviceNameCamel,
                $stripe ? '' : $serviceNameSnake,
                $stripe ? '' : $serviceNameKebab,
                $stripe ? '' : $serviceNameWords,
                $stripe ? '' : $serviceNamePlural,
                $stripe ? '' : $serviceNamePluralSnake,
                $stripe ? '' : $serviceNamePluralCamel,
                $stripe ? '' : $serviceNamePluralKebab,
                $stripe ? '' : $serviceNamePluralWords,
                $stripe ? '' : $migrationTimestamp
            ],
            $string
        );
    }
}
