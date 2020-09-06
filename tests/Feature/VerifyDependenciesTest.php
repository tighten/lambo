<?php

namespace Tests\Feature;

use App\Actions\VerifyDependencies;
use App\ConsoleWriter;
use App\LamboException;
use Symfony\Component\Process\ExecutableFinder;
use Tests\TestCase;

class VerifyDependenciesTest extends TestCase
{
    private $executableFinder;

    public function setUp(): void
    {
        parent::setUp();
        $this->executableFinder = $this->mock(ExecutableFinder::class);
    }

    /** @test */
    function it_checks_that_required_dependencies_are_available()
    {
        $this->executableFinder
            ->shouldReceive('find')
            ->with('laravel')
            ->andReturn('/path/to/laravel');

        $this->executableFinder
            ->shouldReceive('find')
            ->with('valet')
            ->andReturn('/path/to/valet');

        $this->executableFinder
            ->shouldReceive('find')
            ->with('git')
            ->andReturn('/path/to/git');

        app(VerifyDependencies::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_laravel_is_missing()
    {
        $this->dependencyIsMissing('laravel');
        $this->dependencyIsAvailable('valet');
        $this->dependencyIsAvailable('git');

        $this->expectException(LamboException::class);

        app(VerifyDependencies::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_valet_is_missing()
    {
        $this->dependencyIsAvailable('laravel');
        $this->dependencyIsMissing('valet');
        $this->dependencyIsAvailable('git');

        $this->expectException(LamboException::class);

        app(VerifyDependencies::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_git_is_missing()
    {
        $this->dependencyIsAvailable('laravel');
        $this->dependencyIsAvailable('valet');
        $this->dependencyIsMissing('git');

        $this->expectException(LamboException::class);

        app(VerifyDependencies::class)();
    }

    private function dependencyIsAvailable(string $dependency, $isAvailable = true): void
    {
        $foo = $this->executableFinder
            ->shouldReceive('find')
            ->with($dependency);

        $isAvailable
            ? $foo->andReturn("/path/to/{$dependency}")
            : $foo->andReturnNull();
    }

    private function dependencyIsMissing(string $dependency)
    {
        $this->dependencyIsAvailable($dependency, false);
    }
}
