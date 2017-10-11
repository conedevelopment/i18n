# I18n

This little library is a lighweight implementation of Laravel's translation system.

The *"Translation Strings as Keys"* way is not supported yet!

To read the guide how to extract your Laravel translations to the JS side,
what we can use easily with this package, we suggest to read this post:
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

### ``trans()``



### ``trans_choice()``
