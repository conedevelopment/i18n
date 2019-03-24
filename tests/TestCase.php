<?php

namespace Pine\I18n\Test;

use Pine\I18n\I18nServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        View::addNamespace('i18n', __DIR__.'/views');

        Route::get('/i18n', function () {
            return view('i18n::translations');
        });
    }

    protected function getPackageProviders($app)
    {
        return [I18nServiceProvider::class];
    }
}
