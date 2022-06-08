<?php

namespace Core\Routing;

use Illuminate\Support\Str;

abstract class Router
{
    /**
     * The Router intance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Create new Router instance instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->router = app('router');
    }
}
