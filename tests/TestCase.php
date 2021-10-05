<?php

namespace Tests;

use ReflectionProperty;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use AwStudio\TranslatableRoutes\TranslatableRoutesServiceProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            TranslatableRoutesServiceProvider::class
        ];
    }

    /**
     * Create new closure mock.
     *
     * @return ClosureMock
     */
    public function mockClosure()
    {
        return new ClosureMock;
    }

    /**
     * Set protected or private class property value.
     *
     * @param  mixed  $instance
     * @param  string $property
     * @param  mixed  $value
     * @return void
     */
    public function setUnaccessibleProperty($instance, string $property, $value)
    {
        $reflection = new ReflectionProperty(get_class($instance), $property);
        $reflection->setAccessible(true);
        $value = $reflection->setValue($instance, $value);
    }
}
