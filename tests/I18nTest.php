<?php

namespace Pine\I18n\Test;

class I18nTest extends TestCase
{
    /** @test */
    public function a_user_has_translations_on_the_front_end()
    {
        $this->get('/i18n')
            ->assertSee(json_encode(trans('auth')));
    }
}
