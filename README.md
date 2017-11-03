# I18n

Push your Laravel translations to the front-end and use them easily with JavaScript.

The "Translation Strings as Keys" way is not supported!

If you have any question how the package works, we suggest to read this post:
[Using Laravel’s Localization in JS](https://pineco.de/using-laravels-localization-js/).

## Getting started

`composer require thepinecode/i18n`

### (Laravel < 5.5)

add the service provider to `config/app.php`

```php
'providers' => [
    Pine\I18n\I18nServiceProvider::class,
]
```

### Disable the autodiscovery for the package (Laravel 5.5)

In some cases you may disable autodiscovery for this package.
You can add the provider class to the `dont-discover` array to disable it.

Then you can manually register it like above.

## Translations in view files

you can use the `@translations` blade directive.

```html
@translations

<!-- The result -->
<script>window.translations = {{ $translations->toJson() }}</script>
```

You may override the default key for the translations. You can do that by passing a string to the blade directive.

```html
@translations('myTranslations')

<!-- The result -->
<script>window.myTranslations = {{ $translations->toJson() }}</script>
```

## Publishing and using the JavaScript library

You can publish the JS file, Use the `php artisan vendor:publish --tag=i18n-js`

After publishing you can find your fresh copy in the `resources/assets/vendor/i18n` folder.

### Using the I18n.js

Then you can import the *I18n* class and assign it to the `window` object.

```js
import I18n from './../vendor/i18n/I18n';
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
let traslator = new I18n('myTranslations');
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

The package comes with two methods on JS side. The `trans()` and the `trans_choice()``.

#### `trans()``

The `trans` method accepts the key of the translation and the attributes what we want to replace, but it's optional.

```js
translator.trans('auth.failed');

// These credentials do not match our records.

translator.trans('auth.throttle', { seconds: 60 });

// Too many login attempts. Please try again in 60 seconds.
```

#### `trans_choice()``

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
