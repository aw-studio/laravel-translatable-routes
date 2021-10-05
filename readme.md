# Laravel Translatable Routes

A package for creating and managing translatable routes in laravel.

## Setup

Install the package via composer:

```shell
composer require aw-studio/laravel-translatable-routes
```

## Usage

Build translated routes in the form of `/en/home`, `/de/startseite` made easy.

Make sure to translate your routes within your translation-files in the
`resources` directory, for example:

```php
// lang/de/routes.php

return [
    'home' => 'startseite'
];
```

You can now simply add translated Routes to your preferred routes file using the
`__()` helper in the uri string like this:

```php
Route::trans('/__(routes.home)', 'HomeController@show')->name('home');
```

A translated route will be created for all locales defined in your
`translatable` config. The locale will be prepended to the given **uri** and
**name**. So the routes for the previous example would be:

-   `en.home` => `/en/home`
-   `de.home` => `/de/startseite`

The `__route` helper prepends the current locale to the given name and returns
the corresponding route:

```php
<a href="{{ __route('home') }}">
    ...
</a>
```

### Switching Languages

You may want to place a link to the different language route on your website. To
do this, the `translate` method can be applied to the current route with the
desired locale.

```php
<a href="{{ Request::route()->translate('de') }}">Deutsch</a>
```

#### With Parameters

For routes with parameters a translator must be specified. A translator is a
controller method that returns the route parameters for the desired locale. The
translator method receives the desired locale and the parameters for the current
locale.

```php
// ./routes/web.php
Route::trans('/{slug}', 'PostController@show')->translator('getSlug')->name('home');

// ./app/Http/Controllers/PostController.php

class PostController extends Controller
{
	// ...

	public function getSlug($locale, $slug)
	{
		$slug = Post::whereTranslation('slug', $slug)
			->first()
			->translate($locale)
			->slug;

		return ['slug' => $slug];
	}
}
```
