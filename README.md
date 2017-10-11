# I18n

This library is a lighweight implementation of Laravel's translation system.

The *"Translation Strings as Keys"* way is not supported yet!

To read the guide how to extract your Laravel translations to the JS side,
and use them easily with this package, we suggest to read this post:
[Using Laravel’s Localization in JS](https://pineco.de/using-laravels-localization-js/).

## Getting started

Download the file and copy it to your project folder.
Then you can import the *I18n* class and assign it to the ``window`` object.

```js
import I18n from './I18n';
window.I18n = I18n;
```

### Initializing a translation instance

From this point you can initialize the translation service anywhere from your applivation.

```js
let translator = new I18n(yourTranslationObject);
```

### Using it as a Vue service

If you want to use it from Vue templates directly you can extend Vue with this easily.

```js
Vue.prototype.$I18n = new I18n(yourTranslationObject);
```

You can call it from your template or the script part of your component like below:

```js
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

## Methods

In the examples the following translation object is used, we passed this object to the translator via the constructor.

```js
let translations = {
    auth: {
        failed: 'Please try to login again!',
        error: 'The :attribute was incorrect!',
        throttle: 'Be careful, you have :attempts attempt left.|You still have :attempts attempts left.'
    }
};

let translatior = new I18n(translations);
```
> Note, the plural and the singular verions are separated with the ``|`` character!


### ``trans()``

The ``trans`` method accepts the key of the translation and the attributes what we want to replace, but it's optional.

```js
translator.trans('auth.failed');

// Please try to login again!

translator.trans('auth.error', { attribute: 'email' });

// The email was incorrect!
```

### ``trans_choice()``

The ``trans_choice()`` method determines if the translation should be pluralized or nor by the given cout.
Also, it accepts the attributes we want to replace.

```js
translator.trans_choice('auth.throttle', 1, { attempts: 'only one' });

// Be careful, you have only one attempt left.

translator.trans_choice('auth.throttle', 4, { attempts: 'less than five' });

// You still have less than five attempts left.
```
