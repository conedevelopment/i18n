<?php

namespace Pine\I18n;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
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
        // Publish configuration files and assets
        $this->publishes([
            __DIR__ . '/config/i18n.php' => config_path('i18n.php'),
            __DIR__ . '/../resources/js' => resource_path('js/vendor'),
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
     * @return Collection
     */
    protected function translations()
    {
        $resolvedTranslations = $this->resolvedTranslations();
        $filtering = config('i18n.filtering');

        if (isset($filtering)) {
            return $this->filterTranslations($resolvedTranslations, $filtering);
        }

        return $resolvedTranslations;
    }

    /**
     * Get the resolved translations.
     *
     * The resolved translations are a merge of the app translations
     * with package translations. Duplicated and overides are taken
     * care of.
     *
     * @return Collection
     */
    protected function resolvedTranslations()
    {
        $appTranslations = $this->appTranslations();
        $packageTranslations = $this->packageTranslations();

        return $appTranslations->keys()
            ->merge($packageTranslations->keys())
            ->unique()
            ->values()
            ->mapWithKeys(function ($locale) use ($appTranslations, $packageTranslations) {
                return [
                    $locale => $appTranslations->has($locale)
                        ? $appTranslations->get($locale)->merge($packageTranslations->get($locale))
                        : $packageTranslations->get($locale)->merge($appTranslations->get(config('app.fallback_locale'))),
                ];
            });
    }

    /**
     * Get the app translations.
     *
     * @return Collection
     */
    protected function appTranslations()
    {
        return collect(File::directories(resource_path('lang')))->mapWithKeys(function ($dir) {
            return [
                basename($dir) => collect($this->getFiles($dir))->flatMap(function ($file) {
                    return [
                        $file->getBasename('.php') => (include $file->getPathname()),
                    ];
                }),
            ];
        });
    }

    /**
     * Get the package translations.
     *
     * @return Collection
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
            });
        })->reduce(function ($collection, $item) {
            return $collection->mergeRecursive($item);
        }, collect());
    }

    /**
     * Get the files of the given directory.
     *
     * @param string $dir
     * @return array
     */
    protected function getFiles($dir)
    {
        if (is_dir($dir)) {
            return File::files($dir);
        }

        return [];
    }

    /**
     * Return only the filtered translations. Two modes are available: whitelist and
     * blacklist.
     *
     * @param Collection $translations The complete translations collection.
     * @param string $mode The filtering mode to use.
     * @return Collection
     */
    private function filterTranslations(Collection $translations, string $mode)
    {
        if (!in_array($mode, ['blacklist', 'whitelist'])) {
            return $translations;
        }

        $filters = config('i18n.' . $mode);

        $method = $mode === 'whitelist' ? 'only' : 'except';

        return $translations->map(function ($files) use ($method, $filters) {
            return collect($files)->$method($filters);
        });
    }
}
