<?php

namespace Pine\I18n\Tests;

use Illuminate\Contracts\Console\Kernel;
use Pine\I18n\I18nServiceProvider;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->booting(function () use ($app) {
            $app->register(I18nServiceProvider::class);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
