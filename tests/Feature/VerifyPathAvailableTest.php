<?php

namespace Tests\Feature;

use App\Actions\VerifyPathAvailable;
use App\LamboException;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class VerifyPathAvailableTest extends TestCase
{

    /** @test */
    function it_checks_if_the_required_directories_are_available()
    {
        Config::set('lambo.store.root_path', '/some/filesystem/path');
        Config::set('lambo.store.project_path', '/some/filesystem/path/my-project');

        File::shouldReceive('isDirectory')
            ->with('/some/filesystem/path')
            ->once()
            ->andReturn(true);

        File::shouldReceive('isDirectory')
            ->with('/some/filesystem/path/my-project')
            ->once()
            ->andReturn(false);

        app(VerifyPathAvailable::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_the_root_path_is_not_available()
    {
        Config::set('lambo.store.root_path', '/non/existent/filesystem/path');

        File::shouldReceive('isDirectory')
            ->with('/non/existent/filesystem/path')
            ->once()
            ->andReturn(false);

        $this->expectException(LamboException::class);

        app(VerifyPathAvailable::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_the_project_path_already_exists()
    {
        Config::set('lambo.store.root_path', '/some/filesystem/path');
        Config::set('lambo.store.project_path', '/some/filesystem/path/existing-directory');

        File::shouldReceive('isDirectory')
            ->with('/some/filesystem/path')
            ->once()
            ->andReturn(true)
            ->globally()
            ->ordered();

        File::shouldReceive('isDirectory')
            ->with('/some/filesystem/path/existing-directory')
            ->once()
            ->andReturn(true)
            ->globally()
            ->ordered();;

        $this->expectException(LamboException::class);

        app( VerifyPathAvailable::class)();
    }

    /** @test */
    function it_throws_an_exception_if_project_path_is_empty()
    {
        Config::set('lambo.store.root_path', '/some/filesystem/path');
        Config::set('lambo.store.project_path', '');

        File::shouldReceive('isDirectory')
            ->with('/some/filesystem/path')
            ->once()
            ->andReturn(true)
            ->globally()
            ->ordered();

        $this->expectException(Exception::class);

        app( VerifyPathAvailable::class)();
    }

    /** @test */
    function it_throws_an_exception_if_project_path_is_null()
    {
        Config::set('lambo.store.root_path', '/some/filesystem/path');
        Config::set('lambo.store.project_path', null);

        File::shouldReceive('isDirectory')
            ->with('/some/filesystem/path')
            ->once()
            ->andReturn(true)
            ->globally()
            ->ordered();

        $this->expectException(Exception::class);

        app( VerifyPathAvailable::class)();
    }
}
