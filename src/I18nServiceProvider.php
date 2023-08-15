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
    public function boot(): void
    {
        // Publish the assets
        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js/vendor'),
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
    protected function translations(): Collection
    {
        $path = null;

        if (is_dir(base_path('lang'))) {
            $path = base_path('lang');
        } elseif (is_dir(resource_path('lang'))) {
            $path = resource_path('lang');
        } elseif (is_dir(base_path('vendor/laravel/framework/src/Illuminate/Translation/lang'))) {
            $path = base_path('vendor/laravel/framework/src/Illuminate/Translation/lang');
        }

        $translations = is_null($path) ? collect() : collect(File::directories($path))->mapWithKeys(function ($dir) {
            return [
                basename($dir) => collect($this->getFiles($dir))->flatMap(function ($file) {
                    return [
                        $file->getBasename('.php') => (include $file->getPathname()),
                    ];
                }),
            ];
        });

        $jsonTranslations = $this->jsonTranslations($path);

        $packageTranslations = $this->packageTranslations();

        return $translations->keys()
                            ->merge($packageTranslations->keys())
                            ->merge($jsonTranslations->keys())
                            ->unique()
                            ->values()
                            ->mapWithKeys(function ($locale) use ($translations, $jsonTranslations, $packageTranslations) {
                                $locales = array_unique([
                                    $locale,
                                    config('app.fallback_locale'),
                                ]);

                                /*
                                 * Laravel docs describe the following behavior:
                                 *
                                 * - Package translations may be overridden with app translations:
                                 *      https://laravel.com/docs/10.x/localization#overriding-package-language-files
                                 * - Does a JSON translation file redefine a translation key used by a package or a
                                 *      PHP defined translation, the package defined or PHP defined tarnslation will be
                                 *      overridden:
                                 *      https://laravel.com/docs/10.x/localization#using-translation-strings-as-keys
                                 *          (Paragraph "Key / File conflicts")
                                 */
                                $prioritizedTranslations = [
                                    $packageTranslations,
                                    $translations,
                                    $jsonTranslations,
                                ];

                                $fullTranslations = collect();
                                foreach ($prioritizedTranslations as $t) {
                                    foreach ($locales as $l) {
                                        if ($t->has($l)) {
                                            $fullTranslations = $fullTranslations->replace($t->get($l));
                                            break;
                                        }
                                    }
                                }

                                return [
                                    $locale => $fullTranslations,
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
                        $namespace.'::' => collect($this->getFiles($dir))->flatMap(function ($file) {
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
     * Get the application json translation files.
     *
     * @param string $dir Path to the application active lang dir.
     * @return \Illuminate\Support\Collection
     */
    protected function jsonTranslations($dir)
    {
        return collect(File::glob($dir . '/*.json'))
            ->mapWithKeys(function ($path) {
                return [
                    basename($path, '.json') => json_decode(
                        json: file_get_contents($path),
                        associative: true,
                        flags: JSON_THROW_ON_ERROR,
                    ),
                ];
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
        return is_dir($dir) ? File::files($dir) : [];
    }
}
