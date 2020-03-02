<?php

namespace Tests\Feature;

use App\Actions\CustomizeDotEnv;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CustomizeDotEnvTest extends TestCase
{
    /** @test */
    function it_saves_the_customized_dot_env_files()
    {
        app()->bind('console', function () {
            return new class{
                public function comment(){}
                public function info(){}
            };
        });

        Config::set('lambo.store.project_name', 'my-project');
        Config::set('lambo.store.database_name', 'my_project');
        Config::set('lambo.store.project_url', 'http://my-project.example.com');
        Config::set('lambo.store.database_username', 'username');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.project_path', '/some/project/path');

        $originalDotEnv = File::get(base_path('tests/Feature/Fixtures/.env.original'));
        $customizedDotEnv = File::get(base_path('tests/Feature/Fixtures/.env.customized'));

        File::shouldReceive('get')
            ->once()->with('/some/project/path/.env.example')
            ->andReturn($originalDotEnv);

        File::shouldReceive('put')
            ->with('/some/project/path/.env.example', $customizedDotEnv);

        File::shouldReceive('put')
            ->with('/some/project/path/.env', $customizedDotEnv);

        (new CustomizeDotEnv)();
    }

    /** @test */
    function it_replaces_static_strings()
    {
        config()->set('lambo.store.database_username', 'root');

        $customizeDotEnv = new CustomizeDotEnv;
        $contents = "DB_USERNAME=previous";
        $contents = $customizeDotEnv->customize($contents);
        $this->assertEquals("DB_USERNAME=root", $contents);
    }

    /** @test */
    function un_targeted_lines_are_unchanged()
    {
        config()->set('lambo.store.database_username', 'root');

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
