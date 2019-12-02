<?php

namespace Pine\I18n\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Pine\I18n\I18nServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        View::addNamespace('i18n', __DIR__.'/views');

        Route::get('/i18n/{view}', function ($view) {
            return view("i18n::{$view}");
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['translator']->addNamespace('i18n', __DIR__.'/lang');
    }

    protected function getPackageProviders($app)
    {
        return [I18nServiceProvider::class];
    }
}
