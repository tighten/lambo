<?php

namespace Tests\Feature;

use App\Actions\CustomizeDotEnv;
use Tests\TestCase;

class CustomizeDotEnvTest extends TestCase
{
    /** @test */
    function it_replaces_static_strings()
    {
        $customizeDotEnv = new CustomizeDotEnv;
        $contents = "DB_USERNAME=previous";
        $contents = $customizeDotEnv->customize($contents);
        $this->assertEquals("DB_USERNAME=root", $contents);
    }

    /** @test */
    function it_replaces_dashes_with_underscores_in_database_names()
    {
        config()->set('lambo.store.project_name', 'with-dashes');

        $customizeDotEnv = new CustomizeDotEnv;
        $contents = "DB_DATABASE=previous";
        $contents = $customizeDotEnv->customize($contents);
        $this->assertEquals("DB_DATABASE=with_dashes", $contents);
    }

    /** @test */
    function un_targeted_lines_are_unchanged()
    {
        $customizeDotEnv = new CustomizeDotEnv;
        $contents = "DB_USERNAME=previous\nDONT_TOUCH_ME=cant_touch_me";
        $contents = $customizeDotEnv->customize($contents);
        $this->assertEquals("DB_USERNAME=root\nDONT_TOUCH_ME=cant_touch_me", $contents);
    }

    /** @test */
    function lines_with_no_equals_are_unchanged()
    {
        $customizeDotEnv = new CustomizeDotEnv;
        $contents = "SOME_VALUE=previous\nABCDEFGNOEQUALS";
        $contents = $customizeDotEnv->customize($contents);
        $this->assertEquals("SOME_VALUE=previous\nABCDEFGNOEQUALS", $contents);
    }

    /** @test */
    function line_breaks_remain()
    {
        $customizeDotEnv = new CustomizeDotEnv;
        $contents = "A=B\n\nC=D";
        $contents = $customizeDotEnv->customize($contents);
        $this->assertEquals("A=B\n\nC=D", $contents);
    }
}
