<?php

if (! function_exists('__route')) {
    /**
     * Get translated route.
     *
     * @param  string  $string
     * @param  array  $parameters
     * @param  bool  $absolue
     * @param  string|null  $locale
     * @return string|null
     */
    function __route($string, $parameters = [], $absolue = true, $locale = null)
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $name = $locale.'.'.$string;

        return route($name, $parameters, $absolue);
    }
}
