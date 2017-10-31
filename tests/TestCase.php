<?php

namespace Pine\I18n\Test;

use Pine\I18n\I18nServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        View::addNamespace('i18n', __DIR__.'/views');
        
        Route::get('/i18n/allowed', function () { return view('i18n::allowed'); });
        Route::get('/i18n/disabled', function () { return view('i18n::disabled'); });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('i18n.views', 'i18n::allowed');
    }

    protected function getPackageProviders($app)
    {
        return [I18nServiceProvider::class];
    }
}
