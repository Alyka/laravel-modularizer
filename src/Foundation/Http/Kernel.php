<?php

namespace Modularizer\Foundation\Http;

use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Foundation\Http\Kernel as BaseKernel;

abstract class Kernel extends BaseKernel
{
    /**
     * Register all the http middleware.
     *
     * @return void
     */
    public function registerAllMiddleware()
    {
        $this->registerGlobalMiddleware();
        $this->registerMiddlewareGroup();
        $this->registerRouteMiddleware();
    }

    /**
     * @inheritDoc
     */
    public function bootstrap()
    {
        $this->registerAllMiddleware();
        parent::bootstrap();
    }

    /**
     * Register all global http middleware.
     *
     * @return void
     */
    protected function registerGlobalMiddleware()
    {
        foreach ($this->middleware as $middleware) {
            $this->app[KernelContract::class]
                 ->pushMiddleware($middleware);
        }
    }

    /**
     * Register all http middleware groups.
     *
     * @return void
     */
    protected function registerMiddlewareGroup()
    {
        foreach ($this->middlewareGroups as $groupName => $group) {
            foreach ($group as $middleware) {
                $this->app[KernelContract::class]
                     ->appendMiddlewareToGroup($groupName, $middleware);
            }
        }
    }

    /**
     * Register all route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        foreach ($this->routeMiddleware as $alias => $middleware) {
            $this->router->aliasMiddleware($alias, $middleware);
        }
    }
}
