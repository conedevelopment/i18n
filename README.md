# I18n

Push your Laravel translations to the front end and use them easily with JavaScript.

A useful tool for SPAs and front-end-heavy applications.

If you have any questions about how the package works, we suggest reading this post:
[Using Laravel’s Localization in JS](https://pineco.de/using-laravels-localization-js/).

## Getting started

Install the package with Composer by running `composer require conedevelopment/i18n`.

## Translations in view files

You can use the `@translations` blade directive.
This directive automatically wraps the translations to a `<script>` tag.

```html
@translations

<!-- The result -->
<script>window['translations'] = { auth: {...}, validation: {...} }</script>
```

You may override the default key for the translations. You can do that by passing a string to the blade directive.

```html
@translations ('myTranslations')

<!-- The result -->
<script>window['myTranslations'] = { auth: {...}, validation: {...} }</script>
```

## Publishing and using the JavaScript library

Run `php artisan vendor:publish` and choose the `Pine\I18n\I18nServiceProvider` provider.
After publishing, you can find the generated file in the `resources/js/vendor` directory.

### Using the I18n.js

You can then import the `I18n` class and assign it to the `window` object.

```js
import I18n from './vendor/I18n';
window.I18n = I18n;
```

### Initializing a translation instance

You can initialize the translation service anywhere in your application.

```js
let translator = new I18n;
```

By default, it uses the `translations` key in the `window` object.
If you want to use the custom key you set in the Blade directive, pass the same key to the constructor.

```js
let translator = new I18n('myTranslations');
```

`trans_choice()` falls back to the first form when the count is `1` and to the second form otherwise,
unless an explicit selector like `{0}` or `[2,9]` matches first.

### Using it as a Vue service

If you want to use it directly in Vue templates, you can extend Vue like this:

```js
Vue.prototype.$I18n = new I18n;
```

You can call it from your template or from the script section of your component:

```html
<template>
    <div>{{ $I18n.trans('some.key') }}</div>
</template>
```

```js
computed: {
    translations: {
        something: this.$I18n.trans('some.key')
    }
}
```

### Methods

The package provides two JavaScript methods: `trans()` and `trans_choice()`.

#### `trans()`

The `trans` method accepts the translation key and an optional object of replacement values.

```js
translator.trans('auth.failed');

// These credentials do not match our records.

translator.trans('auth.throttle', { seconds: 60 });

// Too many login attempts. Please try again in 60 seconds.
```

#### `trans_choice()`

The `trans_choice` method selects the correct plural form for the given count.
It also accepts an object of replacement values.

Let's say we have the following translation line:

```php
[
    'attempts' => 'Be careful, you have :attempts attempt left.|You still have :attempts attempts left.',
]
```
> [!NOTE]
> The plural and the singular versions are separated by the `|` character!

```js
translator.trans_choice('auth.attempts', 1, { attempts: 'only one' });

// Be careful, you have only one attempt left.

translator.trans_choice('auth.attempts', 4, { attempts: 'less than five' });

// You still have less than five attempts left.
```

As in Laravel, you can define explicit pluralization ranges.
You can also replace placeholders just like in `trans()`.

```php
[
    'apples' => '{0} There are none|[1,19] There are some (:number)|[20,*] There are many (:number)',
]
```
> You can separate more than two choices with the `|` character.

```js
translator.trans_choice('messages.apples', 0);

// There are none

translator.trans_choice('auth.attempts', 8, { number: 8 });

// There are some (8)

translator.trans_choice('auth.attempts', 25, { number: 25 });

// There are many (25)
```

### Transforming replacement parameters

Like Laravel, you can transform replacement values to uppercase or capitalize only the first letter.
You only need to change the placeholder casing.

```php
[
    'welcome' => 'Welcome, :NAME',
    'goodbye' => 'Goodbye, :Name',
]
```
> If you want, you can pass the same parameter with different
> modifiers in one line as well, like `:NAME`, `:name` or `:Name`.

```js
translator.trans('messages.welcome', { name: 'pine' });

// Welcome, PINE

translator.trans('messages.goodbye', { name: 'pine' });

// Goodbye, Pine
```

### Package translations

Thanks to the idea of [Jonathan](https://github.com/sardoj), package translations are supported by default.
You can access package translations the same way you do in Laravel, using the predefined namespace.

```js
translator.trans('courier::messages.message');
```

## Multiple locales

Multiple locales are supported. You can change the application's locale at any time.
Behind the scenes, the correct translations are rendered when they exist.

## Fallback locales

If translations are not available in the current locale,
the package will look for translations in the fallback locale.
If they are not available there either, the missing translations will not be rendered.

## Performance

Translations are generated when the views are compiled.
That means they are cached and stored as strings in the compiled views.
This is much more efficient than generating them at runtime or making an AJAX request to fetch them.

Behind the scenes, a switch statement determines which translations should be present based on the current locale.
This way, only the current locale's translations are pushed to the `window` object instead of all translations.

> Note: In the local environment, cached views are cleared to keep translations fresh.

## Contribute

If you find a bug or have an idea for the package, feel free to open an issue.
