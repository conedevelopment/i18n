<?php

namespace Pine\I18n;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class I18nServiceProvider extends ServiceProvider
{
    protected $lang_path;

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->lang_path = resource_path('lang');

        // Publish the assets
        $this->publishes([
            __DIR__ . '/../resources/assets/js' => resource_path('assets/vendor/i18n'),
        ], 'i18n-js');

        // Register the custom blade directive
        Blade::directive('translations', function ($name) {
            $name = str_replace("'", '', $name ?: 'translations');
            $data = collect($this->getTranslations())->toJson();

            return
<<<EOT
<script>
    var lang = '<?php echo app()->getLocale() ?>'
    window.$name = $data
</script>
EOT;
        });
    }

    /**
     * Get the translations.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTranslations()
    {
        $locales       = $this->getLocales();
        $defaultLocale = array_key_exists('en', $locales) ? 'en' : $locales[0];
        $defaultFiles  = app('files')->files($this->lang_path . "/$defaultLocale");
        $res           = [];

        foreach ($defaultFiles as $file) {
            $name = $file->getBasename('.php');

            foreach ($locales as $locale) {
                $dir  = resource_path("lang/$locale/$name.php");
                $data = include $dir;
                $keys = array_keys($data);

                foreach ($keys as $key) {
                    $res[$name][$key][$locale] = trans("$name.$key", [], $locale);
                }
            }
        }

        return $res;
    }

    protected function getLocales()
    {
        $locales = [];

        foreach (app('files')->directories($this->lang_path) as $dir) {
            if (ends_with($dir, 'vendor')) {
                continue;
            }

            $locales[] = substr($dir, strrpos($dir, '/') + 1);
        }

        return $locales;
    }
}
