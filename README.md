# I18n

Push your Laravel translations to the front-end and use them easily with JavaScript.

The "Translation Strings as Keys" way is not supported!

If you have any question how the package works, we suggest to read this post: 
[Using Laravel’s Localization in JS](https://pineco.de/using-laravels-localization-js/).

## Getting started

You can install the package with composer, running the ``composer require thepinecode/i18n`` command.

### Laravel 5.5

If you are using version 5.5, there is nothing else to do.
Since the package supports autodiscovery, Laravel will register the service provider automatically behind the scenes.

### Laravel 5.4 and below

You have to register the service provider manually.
Go to the ``config/app.php`` file and add the ``Pine\I18n\I18nServiceProvider::class`` to the providers array.

### Disable the autodiscovery for the package

In some cases you may disable autodiscovery for this package.
You can add the provider class to the ``dont-discover`` array to disable it.

Then you need to register it manually again.

## Configuration

You may override the default configurations. 
To do that, first you have to publish the config file with the following command:
``php artisan vendor:publish`` and use the ``i18n-config`` tag to copy the config file.

## Translations in view files

You can access to the ``$translations`` variable, anywhere in your view files. 
It's a collection instance, so you have the flexibility what the collection service provides:

```html
<script>
    window.translations = {{ $translations->toJson() }};
    // or
    window.translations = @json($translations);
</script>
```

Also, you can use the ``@translations`` blade directive.
It's automatically converted to JSON, so it's a neat way to print your translations out:

```html
<script>
    window.translations = @translations;
</script>
```

You can set the specific views where you want to share the translations in the configuration file.
You can set a single value or an array of values.

## Publishing and using the JavaScipt library

You can publish the JS file like the config file, but you need to use a different tag.
Use the ``php artisan vendor:publish`` command and choose the ``i18n-js`` tag.
After publishing you can find your fresh copy in the ``resources/assets/js/vendor/i18n`` folder.

### Using the I18n.js

Then you can import the *I18n* class and assign it to the ``window`` object.

```js
import I18n from './vendor/i18n/I18n';
window.I18n = I18n;
```

### Initializing a translation instance

From this point you can initialize the translation service anywhere from your application.

You can pass any kind of object you want to the constructor. 
It's useful if you need multiple translator instances on the JS side.

```js
let translator = new I18n(window.translations);
```

### Using it as a Vue service

If you want to use it from Vue templates directly you can extend Vue with this easily.

```js
Vue.prototype.$I18n = new I18n(window.translations);
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

The package comes with two methods on JS side. The ``trans()`` and the ``trans_choice()``.

#### ``trans()``

The ``trans`` method accepts the key of the translation and the attributes what we want to replace, but it's optional.

```js
translator.trans('auth.failed');

// These credentials do not match our records.

translator.trans('auth.throttle', { seconds: 60 });

// Too many login attempts. Please try again in 60 seconds.
```

#### ``trans_choice()``

The ``trans_choice()`` method determines if the translation should be pluralized or nor by the given cout.
Also, it accepts the attributes we want to replace.

Let's say we have the following translation line:
```php
[
    'attempts' => 'Be careful, you have :attempts attempt left.|You still have :attempts attempts left.',
]
```
> Note, the plural and the singular verions are separated with the ``|`` character!

```js
translator.trans_choice('auth.attempts', 1, { attempts: 'only one' });

// Be careful, you have only one attempt left.

translator.trans_choice('auth.attempts', 4, { attempts: 'less than five' });

// You still have less than five attempts left.
```
