# I18n

Push your Laravel translations to the front-end and use them easily with JavaScript.

The JSON based translations are not supported!

If you have any question how the package works, we suggest to read this post:
[Using Laravel’s Localization in JS](https://pineco.de/using-laravels-localization-js/).

## Getting started

You can install the package with composer, running the `composer require thepinecode/i18n` command.

### Laravel 5.5 and up

If you are using version 5.5 and up, there is nothing else to do.
Since the package supports autodiscovery, Laravel will register the service provider automatically behind the scenes.

#### Disable the autodiscovery for the package

In some cases you may disable autodiscovery for this package.
You can add the provider class to the `dont-discover` array to disable it.

Then you need to register it manually again.

### Laravel 5.4 and below

You have to register the service provider manually.
Go to the `config/app.php` file and add the `Pine\I18n\I18nServiceProvider::class` to the providers array.

## Translations in view files

You can use the `@translations` blade directive.
This directive automatically wrap the translations to a `<script>` tag.

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

By default, it gets translations from `resource_path('lang')` directory.
If you want to use a custom directory (from a package for example), pass the directory's path to the blade directive and the package's namespace.

```html
@translations ('myTranslations', 'vendor/vendor_name/package/lang', 'myNamespace')
```

## Publishing and using the JavaScript library

Use the `php artisan vendor:publish` command and choose the `Pine\I18n\I18nServiceProvider` provider.
After publishing you can find your fresh copy in the `resources/assets/js/vendor` folder.

### Using the I18n.js

Then you can import the *I18n* class and assign it to the `window` object.

```js
import I18n from './vendor/I18n';
window.I18n = I18n;
```

### Initializing a translation instance

From this point you can initialize the translation service anywhere from your application.

```js
let translator = new I18n;
```

By default, it uses the `translations` key in the `window` object.
If you want to use the custom one you set in the blade directive, pass the same key to the constructor.

```js
let translator = new I18n('myTranslations');
```

By default it uses `.` as namespace separator (e.g. default.hello).
If you want to use `.` in your translation keys, you can change this separator.

```js
let translator = new I18n('myTranslations', ':');

translator.trans('default:hello.word');
```

### Using it as a Vue service

If you want to use it from Vue templates directly you can extend Vue with this easily.

```js
Vue.prototype.$I18n = new I18n;
```

You can call it from your template or the script part of your component like below:

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

The package comes with two methods on JS side. The `trans()` and the `trans_choice()`.

#### `trans()`

The `trans` method accepts the key of the translation and the attributes what we want to replace, but it's optional.

```js
translator.trans('auth.failed');

// These credentials do not match our records.

translator.trans('auth.throttle', { seconds: 60 });

// Too many login attempts. Please try again in 60 seconds.
```

#### `trans_choice()`

The `trans_choice` method determines if the translation should be pluralized or nor by the given cout.
Also, it accepts the attributes we want to replace.

Let's say we have the following translation line:

```php
[
    'attempts' => 'Be careful, you have :attempts attempt left.|You still have :attempts attempts left.',
]
```
> Note, the plural and the singular verions are separated with the `|` character!

```js
translator.trans_choice('auth.attempts', 1, { attempts: 'only one' });

// Be careful, you have only one attempt left.

translator.trans_choice('auth.attempts', 4, { attempts: 'less than five' });

// You still have less than five attempts left.
```

Like in Laravel, you have the ability to set ranges for the pluralization.
Also, you can replace placeholders like before.

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

Like in Laravel's functionality, you can transform your parameters to upper case, or convert
only the first character to capital letter. All you need to do, to modify your placeholders.

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

## Contribute

If you found a bug or you have an idea connecting the package, feel free to open an issue.
