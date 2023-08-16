<?php

namespace Pine\I18n\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Pine\I18n\Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        app()->setBasePath(__DIR__ . '/app');

        // clean up default language dir that laravel publishes
        File::deleteDirectory(base_path('/lang/en'));

        $this->app['translator']->addNamespace('i18n', __DIR__.'/lang');

        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        Artisan::call('lang:publish');

        View::addNamespace('i18n', __DIR__.'/views');

        Route::get('/i18n/{view}', function ($view) {
            return view("i18n::{$view}");
        });
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // clean up default language dir that laravel publishes
        File::deleteDirectory(base_path('/lang/en'));
    }
}
