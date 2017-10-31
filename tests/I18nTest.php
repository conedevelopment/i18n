<?php

namespace Pine\I18n\Test;

class I18nTest extends TestCase
{
    /** @test */
    public function a_user_can_access_the_translations_where_its_allowed()
    {
        $this->get('/i18n/allowed')
            ->assertViewHas('translations');
    }

    /** @test */
    public function a_user_cant_access_the_translations_where_its_disabled()
    {
        $this->get('/i18n/disabled')
            ->assertViewMissing('translations');
    }
}
