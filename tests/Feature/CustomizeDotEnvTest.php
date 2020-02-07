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

        File::shouldReceive('get')
            ->once()->with('/some/project/path/.env.example')
            ->andReturn($this->getTestDotEnvFile());

        File::shouldReceive('put')
            ->with('/some/project/path/.env.example', $this->getCustomizedDotEnvFile());

        File::shouldReceive('put')
            ->with('/some/project/path/.env', $this->getCustomizedDotEnvFile());

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

    private function getTestDotEnvFile()
    {  return <<<'TEST_FILE'
APP_NAME=Laravel
APP_URL=http://my-project.example.com

DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
TEST_FILE;
    }

    private function getCustomizedDotEnvFile()
    {  return <<<'TEST_FILE'
APP_NAME=my-project
APP_URL=http://my-project.example.com

DB_DATABASE=my_project
DB_USERNAME=username
DB_PASSWORD=password
TEST_FILE;
    }
}
