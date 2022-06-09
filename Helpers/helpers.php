<?php

use Admin\Providers\AppServiceProvider as AdminServiceProvider;
use Carbon\Carbon;
use Docs\Providers\AppServiceProvider as DocsServiceProvider;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Installer\Providers\AppServiceProvider as InstallerServiceProvider;
use Marketplace\Providers\AppServiceProvider as MarketplaceServiceProvider;

if (! function_exists('readComposerJson')) {

    /**
     * Read the content of the composer.json file.
     * 
     * @return object
     */
    function readComposerJson()
    {
        $composerFile = file_get_contents(
            base_path('composer.json')
        );
        
        return json_decode($composerFile, true);
    }
}

if (! function_exists('getNamespace')) {
    /**
     * Get the namespace of a given directory in the script path.
     *
     * @param string $dir
     * @return string
     *
     * @throws RuntimeException
     */
    function getNamespace($dir)
    {
        $composer = readComposerJson();

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                $basePath = realpath(base_path($pathChoice));
                
                if ($basePath === realpath(base_path($dir))) {
                    return $namespace;
                }
            }
        }

        throw new RuntimeException('The namespace could not be detected automatically.');
    }
}

if (! function_exists('moduleNamespace')) {
    /**
     * Get the namespace associated with the given module.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    function moduleNamespace()
    {
        return 'Modules\\';
    }
}

if (! function_exists('packageNamespace')) {
    /**
     * Get the namespace of the package.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    function packageNamespace()
    {
        return 'Modularizer\\';
    }
}

if (! function_exists('appNamespace')) {
    /**
     * Get the namespace of a given app.
     *
     * @param string $app
     * @return string
     *
     * @throws RuntimeException
     */
    function appNamespace($app)
    {
        $pathChoice = "apps/{$app}/app";

        return getNamespace($pathChoice);
    }
}

if (! function_exists('namespacePath')) {
    /**
     * Get the directory path associated with the specified namespace.
     *
     * @param string $namespace
     * @return string
     *
     * @throws RuntimeException
     */
    function namespacePath($namespace)
    {
        $composer = readComposerJson();

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespaceChoice => $path) {
            if ($namespace === $namespaceChoice) {
                return base_path($path);
            }
        }

        throw new RuntimeException('No path matches the given namespace.');
    }
}

if (! function_exists('classNamespace')) {
    /**
     * Get the namespace of a given class.
     *
     * @param string $class
     * @return string
     */
    function classNamespace($class)
    {
        $composer = readComposerJson();

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            if (Str::startsWith($class, $namespace)) {
                return $namespace;
            }
        }

        throw new RuntimeException('The class namespace could not be detected.');
    }
}

if (! function_exists('classModule')) {
    /**
     * Get the service the given class belongs to.
     *
     * @param string $class
     * @return string
     */
    function classModule($class)
    {
        $namespace = classNamespace($class);

        return Str::before(Str::after($class, $namespace), '\\');
    }
}

if (! function_exists('currentUser')) {
    /**
     * Get the current authenticated user.
     *
     * @param string|null $guard
     * @return User
     */
    function currentUser($guard = null)
    {
        return optional(auth($guard)->user());
    }
}

if (! function_exists('isAdminHost')) {
    /**
     * Determine if the current host is the admin host.
     *
     * @return boolean
     */
    function isAdminHost()
    {
        return Str::startsWith(
            (request()->header('origin') ?? request()->url()),
            config(AdminServiceProvider::NAME.'.url')
        );
    }
}

if (! function_exists('isMarketplaceHost')) {
    /**
     * Determine if the current host is the marketplace host.
     *
     * @return boolean
     */
    function isMarketplaceHost()
    {
        return Str::startsWith(
            (request()->header('origin') ?? request()->url()),
            config(MarketplaceServiceProvider::NAME.'.url')
        );
    }
}

if (! function_exists('isInstallerHost')) {
    /**
     * Determine if the current host is the installer host.
     *
     * @return boolean
     */
    function isInstallerHost()
    {
        return Str::startsWith(
            (request()->header('origin') ?? request()->url()),
            config(InstallerServiceProvider::NAME.'.url')
        );
    }
}

if (! function_exists('isDocHost')) {
    /**
     * Determine if the current host is the doc host.
     *
     * @return boolean
     */
    function isDocHost()
    {
        return Str::startsWith(
            (request()->header('origin') ?? request()->url()),
            config(DocsServiceProvider::NAME.'.url')
        );
    }
}

if (! function_exists('loadRouteFiles')) {
    /**
     * Recursively load all routes files found in the specified path
     * and matches the given type.
     *
     * @param string $path
     * @param string $type file name to match. Does not include extension.
     * @return void
     */
    function loadRouteFiles($path, $type)
    {
        $dirs = File::directories($path);

        foreach ($dirs as $dir) {
            $files = File::files($dir);

            foreach ($files as $file) {
                $fileArr = explode('\\', $file);

                $lastFolder = array_slice($fileArr, -2, 1)[0];
                $basename = end($fileArr);

                $isRouteFile = $lastFolder === 'routes' && $basename === "{$type}.php";

                if ($isRouteFile) {
                    require_once($file);
                }

                if (is_dir($dir)) {
                    loadRouteFiles($dir, $type);
                }
            }
        }
    }
}

if (! function_exists('toMoney')) {
    /**
     * Convert a value to mongod decimal object.
     *
     * @param mixed $value number
     * @return int
     */
    function toMoney($value = 0)
    {
        return toInt($value);
    }
}

if (! function_exists('toInt')) {
    /**
     * Convert a given value to int.
     *
     * @param int|string $value
     * @return int
     */
    function toInt($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return 0;
    }
}

if (! function_exists('makeUuid')) {
    /**
     * Generate a unique uuid.
     *
     * @return string
     */
    function makeUuid()
    {
        $uuid1 = Uuid::uuid1();
        return $uuid1->toString();
    }
}

if (! function_exists('cast')) {
    /**
     * Return an instance of the Cast class.
     *
     * @return Cast
     */
    function cast()
    {
        return app('cast');
    }
}

if (! function_exists('timeFormat')) {
    /**
     * Converts time to a human readable format
     *
     * @param string|Carbon $date
     * @return string time in human readable format
     */
    function timeFormat($date)
    {
        if (!$date) {
            return 'unspecified';
        }
        if (!$date instanceof Carbon) {
            try {
                $date = Carbon::parse($date);
            } catch (Exception $e) {
                return 'unspecified';
            }
        }
        if ($date->diffInHours() <= 3) {
            return $date->diffForHumans();
        } elseif ($date->isYesterday()) {
            return $date->format('h:i:a').' yesterday';
        } elseif ($date->isToday()) {
            return $date->format('h:i:a').' today';
        } elseif ($date->isTomorrow()) {
            return $date->format('h:i:a').' tomorrow';
        } else {
            return $date->diffForHumans();
        }
    }
}
