<?php

namespace Pine\I18n;

use Illuminate\Support\Str;
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
            __DIR__.'/../resources/js' => resource_path('js/vendor'),
        ]);

        // Register the @translations blade directive
        Blade::directive('translations', function ($key) {
            return sprintf(
                '<script>window.%s = %s</script>',
                str_replace(['"', "'"], '', $key ?: 'translations'),
                $this->translations()
            );
        });
    }

    /**
     * Get the translations.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function translations()
    {
        $translations = collect($this->getFiles(resource_path('lang/')))->flatMap(function ($file) {
            return [
                ($translation = $file->getBasename('.php')) => trans($translation),
            ];
        });

        return $translations->merge($this->packageTranslations());
    }

    /**
     * Get the translations.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function packageTranslations()
    {
        return collect($this->app['translator']->getLoader()->namespaces())->flatMap(function ($path, $namespace) {
            return [
                ($namespace .= '::') => collect($this->getFiles($path))->flatMap(function ($file) use ($namespace) {
                    return [
                        ($translation = $file->getBasename('.php')) => trans($namespace . $translation),
                    ];
                }),
            ];
        })->filter(function ($translations) {
            return $translations->isNotEmpty();
        });
    }

    /**
     * Get the files for the given locale.
     *
     * @param  string  $path
     * @return array
     */
    protected function getFiles($path)
    {
        $path = Str::finish($path, '/');

        if (file_exists($path . App::getLocale())) {
            return File::files($path . App::getLocale());
        } elseif (file_exists($path . config('app.fallback_locale'))) {
            return File::files($path . config('app.fallback_locale'));
        }

        return [];
    }
}
