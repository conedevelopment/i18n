<?php

namespace Pine\I18n;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
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
        // Publish the configuration file
        $this->publishes([
            __DIR__.'/../config/i18n.php' => config_path('i18n.php'),
        ], 'i18n-config');

        // Publish the assets
        $this->publishes([
            __DIR__.'/../resources/assets/js' => resource_path('assets/js/vendor/i18n'),
        ], 'i18n-js');

        // Share the translations with the views
        View::composer(config('i18n.views'), function ($view) {
            return $view->with('translations', $this->getTranslations());
        });

        // Register the custom blade directive
        Blade::directive('translations', function () {
            return '<?php echo $translations->toJson(); ?>';
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge the config
        $this->mergeConfigFrom(
            __DIR__.'/../config/i18n.php', 'i18n'
        );
    }

    /**
     * Get the translations.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTranslations()
    {
        return collect(File::files(
            resource_path('lang/'.App::getLocale())
        ))->flatMap(function ($file) {
            return [
                ($translation = $file->getBasename('.php')) => trans($translation),
            ];
        });
    }
}
