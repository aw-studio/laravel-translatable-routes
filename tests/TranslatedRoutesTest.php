<?php

namespace Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class TranslatedRoutesTest extends TestCase
{
    /** */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('translatable.locales', ['de', 'en']);
    }

    /** @test */
    public function it_adds_name_to_each_route()
    {
        $routes = Route::trans('home', fn () => null)->name('home');

        foreach ($routes->getRoutes() as $route) {
            $this->assertStringEndsWith('.home', $route->getName());
        }
    }

    /** @test */
    public function test_getNames_method()
    {
        $routes = Route::trans('home', fn () => null)->name('home');

        $names = $routes->getNames();

        $this->assertInstanceOf(Collection::class, $names);
        $this->assertEquals(['de.home', 'en.home'], $names->toArray());
    }
}
