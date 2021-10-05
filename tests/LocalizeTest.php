<?php

namespace Tests;

use AwStudio\TranslatableRoutes\TranslatedRoutes;
use AwStudio\TranslatableRoutes\TransRoute;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use LogicException;
use Mockery as m;

class LocalizeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('translatable.locales', ['de', 'en']);
    }

    /** @test */
    public function it_creates_a_routes_for_each_locale()
    {
        $this->assertCount(0, Route::getRoutes()->getRoutes());

        Route::trans('home', fn () => null);

        $this->assertCount(2, Route::getRoutes()->getRoutes());
    }

    /** @test */
    public function it_returns_translated_routes()
    {
        $routes = Route::trans('home', fn () => null);

        $this->assertInstanceOf(TranslatedRoutes::class, $routes);
    }

    /** @test */
    public function it_adds_locale_route_prefix()
    {
        Route::trans('home', fn () => null);

        foreach (Route::getRoutes()->getRoutes() as $route) {
            $this->assertContains(explode('/', $route->uri)[0], ['de', 'en']);
        }
    }

    /** @test */
    public function it_prepends_locale_to_name()
    {
        Route::trans('home', fn () => null);

        foreach (Route::getRoutes()->getRoutes() as $route) {
            $this->assertContains(explode('.', $route->getName())[0], ['de', 'en']);
        }
    }

    /** @test */
    public function test_transle_macro()
    {
        $route = Route::trans('home', LocalizeTestController::class)
            ->name('home')
            ->getRoutes()
            ->first();

        $this->setUnaccessibleProperty($route, 'parameters', []);

        Request::setRouteResolver(fn () => $route);

        $this->assertStringEndsWith('de/home', route($route->getName()));
        $this->assertStringEndsWith('en/home', $route->translate('en'));
    }

    /** @test */
    public function test_transle_macro_calls_translator_with_parameters()
    {
        $route = Route::trans('home', LocalizeTestController::class)
            ->name('home')
            ->translator('getSlug')
            ->getRoutes()
            ->first();

        $controller = m::mock(LocalizeTestController::class);
        app()->bind(LocalizeTestController::class, fn () => $controller);

        $this->setUnaccessibleProperty($route, 'parameters', ['slug' => 'hello']);

        Request::setRouteResolver(fn () => $route);

        $controller->shouldReceive('getSlug')->withArgs([app()->getLocale(), 'hello']);

        $route->translate('en');
    }

    /** @test */
    public function test_transle_macro_calls_closure_translator_with_parameters()
    {
        $closure = $this->mockClosure()
            ->withArgs([app()->getLocale(), 'hello'])
            ->andReturn('dummy-params');

        $route = Route::trans('home', LocalizeTestController::class)
            ->name('home')
            ->translator($closure->getClosure())
            ->getRoutes()
            ->first();

        $controller = m::mock(LocalizeTestController::class);
        app()->bind(LocalizeTestController::class, fn () => $controller);

        $this->setUnaccessibleProperty($route, 'parameters', ['slug' => 'hello']);

        Request::setRouteResolver(fn () => $route);

        $this->assertStringEndsWith('dummy-params', $route->translate('en'));

        $closure->assertWasCalled();
    }

    /** @test */
    public function test_translate_fails_when_not_for_current_route()
    {
        $route = Route::trans('home', LocalizeTestController::class)->getRoutes()->first();

        $this->expectException(LogicException::class);
        $route->translate('en');
    }

    /** @test */
    public function test_get_locale_method()
    {
        $this->app['config']['translatable.fallback_locale'] = 'es';
        $transRoute = new TransRoute(['de', 'en', 'es']);
        $this->assertEquals('es', $transRoute->getLocale());
    }

    /** @test */
    public function test_get_locale_method_gets_browser_locale()
    {
        $this->app['config']['translatable.fallback_locale'] = 'es';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9,en-DE;q=0.8,en;q=0.7,en-US;q=0.6';
        $transRoute = new TransRoute(['de', 'en', 'es']);
        $this->assertEquals('de', $transRoute->getLocale());
    }
}

class LocalizeTestController
{
    public function __invoke()
    {
    }
}
