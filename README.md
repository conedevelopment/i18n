# I18n

## Getting Started

You can install the package with composer, using the ``composer require thepinecode/i18n`` command.

### Laravel 5.5

If you are using version 5.5, there is nothing else to do.
Since the package supports autodiscovery, Laravel will register the service provider automatically, behind the scenes.

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
