<?php

namespace AwStudio\TranslatableRoutes;

use Closure;
use LogicException;
use Illuminate\Support\Str;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route as RouteFacade;

class TranslatableRoutesServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('route.trans', function ($app) {
            return new TransRoute($app['config']['translatable.locales'] ?: []);
        });

        // Macros
        $this->registerTransMacro();
        $this->registerTranslateMacro();
        $this->registerTranslatorMacro();
        $this->registerGetNameWithoutLocaleMacro();
    }

    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->afterResolving('translator', function ($app) {
            $this->app->setLocale($this->app['route.trans']->getLocale());
        });
    }

    /**
     * Register the macro.
     *
     * @return void
     */
    protected function registerTransMacro()
    {
        RouteFacade::macro('trans', function ($route, $controller) {
            return app('route.trans')->trans($route, $controller);
        });
    }

    /**
     * Register the macro.
     *
     * @return void
     */
    protected function registerTranslateMacro()
    {
        Route::macro('translate', function ($locale) {
            if (! Request::route() || Request::route()->getName() != $this->getName()) {
                throw new LogicException('You may only translate the current route.');
            }

            if (! $this->translator) {
                return url($this->uri, $this->parameters());
            }

            return call_user_func($this->translator, $locale);
        });
    }

    /**
     * Register the macro.
     *
     * @return void
     */
    protected function registerTranslatorMacro()
    {
        Route::macro('translator', function ($translator) {
            $this->translator = function ($locale) use ($translator) {
                $arguments = [
                    $locale,
                    ...array_values(Request::route()->parameters()),
                ];

                if ($translator instanceof Closure) {
                    $parameters = $translator(...$arguments);
                } else {
                    $parameters = $this->getController()->$translator(...$arguments);
                }

                $name = $this->getNameWithoutLocale();

                return __route($name, $parameters, true, $locale);
            };

            return $this;
        });
    }

    /**
     * Register the macro.
     *
     * @return void
     */
    protected function registerGetNameWithoutLocaleMacro()
    {
        Route::macro('getNameWithoutLocale', function () {
            $name = $this->getName();

            foreach (config('translatable.locales') as $locale) {
                if (Str::startsWith($name, $locale.'.')) {
                    return Str::replaceFirst($locale.'.', '', $name);
                }
            }

            return $name;
        });
    }
}
