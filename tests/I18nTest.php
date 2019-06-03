<?php

namespace Pine\I18n\Test;

use Illuminate\Support\Facades\App;

class I18nTest extends TestCase
{
    /** @test */
    public function translations_can_be_printed_via_blade_directive()
    {
        $this->get('/i18n/translations')->assertSee(json_encode(trans('auth')));
    }

    /** @test */
    public function translations_can_have_custom_key()
    {
        $this->get('/i18n/custom-key')->assertSee('window.custom = ');
    }

    /** @test */
    public function translations_can_be_multilang()
    {
        App::setLocale('hu');
        $this->get('/i18n/translations')->assertSee('"i18n::":{"messages":{"test":"Teszt"}');

        App::setLocale('en');
        $this->get('/i18n/translations')->assertSee('"i18n::":{"messages":{"test":"Test"}');
    }
}
