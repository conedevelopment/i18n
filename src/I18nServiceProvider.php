<?php

namespace Pine\I18n;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class I18nServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the assets
        $this->publishes([
            __DIR__.'/../resources/assets/js' => resource_path('assets/js/vendor'),
        ]);

        // Register the custom blade directive
        Blade::directive('translations', function ($key) {
            return sprintf('<script>window[%s] = %s</script>', $key ?: "'translations'", $this->getTranslations());
        });
    }

    /**
     * Get the translations.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTranslations()
    {
        $files = File::files(resource_path('lang/'.App::getLocale()));

        return collect($files)->flatMap(function ($file) {
            return [
                ($translation = $file->getBasename('.php')) => trans($translation),
            ];
        });
    }
}
