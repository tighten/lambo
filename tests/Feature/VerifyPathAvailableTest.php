<?php

namespace Tests\Feature;

use App\Actions\VerifyPathAvailable;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class VerifyPathAvailableTest extends TestCase
{
    /** @test */
    public function it_checks_if_the_required_directories_are_available()
    {
        $this->fakeLamboConsole();

        config(['lambo.store.root_path' => '/some/filesystem/path']);

        File::shouldReceive('isDirectory')
            ->once()->with('/some/filesystem/path')
            ->andReturn(true);

        config(['lambo.store.project_path' => '/some/filesystem/path/my-project']);

        File::shouldReceive('isDirectory')
            ->once()->with('/some/filesystem/path/my-project')
            ->andReturn(false);

        (new VerifyPathAvailable)();
    }

    /** @test */
    public function it_throws_an_exception_if_the_root_path_is_not_available()
    {
        $this->fakeLamboConsole();

        config(['lambo.store.root_path' => '/non/existent/filesystem/path']);

        File::shouldReceive('isDirectory')
            ->once()->with('/non/existent/filesystem/path')
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('/non/existent/filesystem/path is not a directory');

        (new VerifyPathAvailable)();
    }

    /** @test */
    public function it_throws_an_exception_if_the_project_path_already_exists()
    {
        $this->fakeLamboConsole();

        config(['lambo.store.root_path' => '/some/filesystem/path']);

        File::shouldReceive('isDirectory')
            ->once()->with('/some/filesystem/path')
            ->andReturn(true);

        config(['lambo.store.project_path' => '/some/filesystem/path/existing-directory']);

        File::shouldReceive('isDirectory')
            ->once()->with('/some/filesystem/path/existing-directory')
            ->andReturn(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('/some/filesystem/path/existing-directory is already a directory.');

        (new VerifyPathAvailable)();
    }
}
