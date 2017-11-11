<?php

namespace Pine\I18n\Test;

class I18nTest extends TestCase
{
    /** @test */
    public function a_user_can_access_the_translations_where_its_allowed()
    {
        $this->get('/i18n')
            ->assertSee(json_encode(trans('auth')));
    }
}
