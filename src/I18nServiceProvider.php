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
        Blade::directive('translations', function ($params) {

            $data = explode(',', str_replace(['(',')',' ', "'"], '', $params));

            // If dirpath or namespace is not defined
            for ($i=count($data); $i<3; $i++) {
                $data[] = null;
            }

            list($key, $dirpath, $namespace) = $data;

            return sprintf('<script>window["%s"] = %s</script>', $key ?: "'translations'", $this->translations($dirpath, $namespace));
        });
    }

    /**
     * Get the translations.
     *
     * @param string|null $dirpath
     * @param string|null $namespace
     * @return \Illuminate\Support\Collection
     */
    protected function translations($dirpath = null, $namespace = null)
    {
        if (!is_null($dirpath)) {
            if (strpos($dirpath, base_path()) === false) {
                $dirpath = base_path($dirpath.'/'.App::getLocale());
            }
        } else {
            $dirpath = resource_path('lang/'.App::getLocale());
        }

        if (!is_null($namespace)) {
            $namespace .= '::';
        }

        $files = File::files($dirpath);

        return collect($files)->flatMap(function ($file) use ($namespace) {
            return [
                ($translation = $file->getBasename('.php')) => trans($namespace.$translation),
            ];
        });
    }
}