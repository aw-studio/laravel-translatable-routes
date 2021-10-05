<?php

namespace AwStudio\TranslatableRoutes;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;

class TranslatedRoutes
{
    /**
     * Routes collection.
     *
     * @var Collection
     */
    protected $routes;

    /**
     * Create new TranslatedRoutes instance.
     *
     * @param  Collection  $routes
     * @return void
     */
    public function __construct(Collection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Get routes.
     *
     * @return Collection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Get names for each route.
     *
     * @return void
     */
    public function getNames()
    {
        return $this->routes->map(fn (Route $route) => $route->getName());
    }

    /**
     * Call method on all routes.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->routes->each->$method(...$parameters);

        return $this;
    }
}
