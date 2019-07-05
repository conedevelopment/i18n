<?php

namespace Pine\I18n;

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
            __DIR__ . '/../resources/js' => resource_path(
                version_compare($this->app->version(), '5.7.0', '<')
                ? 'assets/js/vendor' : 'js/vendor'
            ),
        ]);

        // Register the @translations blade directive
        Blade::directive('translations', function ($key) {
            $cases = $this->translations()->map(function ($translations, $locale) {
                return sprintf(
                    config('app.fallback_locale') === $locale
                        ? 'default: echo "%2$s"; break;'
                        : 'case "%1$s": echo "%2$s"; break;',
                    $locale, addslashes($translations)
                );
            })->implode(' ');

            return sprintf(
                '<script>window[%s] = <?php switch (App::getLocale()) { %s } ?>;</script>',
                $key ?: "'translations'", $cases
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
        $translations = collect(File::directories(resource_path('lang')))->mapWithKeys(function ($dir) {
            return [
                basename($dir) => collect($this->getFiles($dir))->flatMap(function ($file) {
                    return [
                        $file->getBasename('.php') => (include $file->getPathname()),
                    ];
                }),
            ];
        });

        $packageTranslations = $this->packageTranslations();

        return $translations->keys()->merge(
            $packageTranslations->keys()
        )->unique()->values()->mapWithKeys(function ($locale) use ($translations, $packageTranslations) {
            return [
                $locale => $translations->has($locale)
                    ? $translations->get($locale)->merge($packageTranslations->get($locale))
                    : $packageTranslations->get($locale)->merge($translations->get(config('app.fallback_locale'))),
            ];
        });
    }

    /**
     * Get the package translations.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function packageTranslations()
    {
        $namespaces = $this->app['translator']->getLoader()->namespaces();

        return collect($namespaces)->map(function ($dir, $namespace) {
            return collect(File::directories($dir))->flatMap(function ($dir) use ($namespace) {
                return [
                    basename($dir) => collect([
                        $namespace . '::' => collect($this->getFiles($dir))->flatMap(function ($file) {
                            return [
                                $file->getBasename('.php') => (include $file->getPathname()),
                            ];
                        })->toArray(),
                    ]),
                ];
            })->toArray();
        })->reduce(function ($collection, $item) {
            return collect(array_merge_recursive($collection->toArray(), $item));
        }, collect())->map(function ($item) {
            return collect($item);
        });
    }

    /**
     * Get the files of the given directory.
     *
     * @param  string  $dir
     * @return array
     */
    protected function getFiles($dir)
    {
        if (is_dir($dir)) {
            return File::files($dir);
        }

        return [];
    }
}
