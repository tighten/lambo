<?php

namespace Tests\Feature;

use App\Actions\VerifyDependencies;
use Mockery;
use Symfony\Component\Process\ExecutableFinder;
use Tests\TestCase;

class VerifyDependenciesTest extends TestCase
{
    /** @test */
    public function it_checks_that_required_dependencies_are_available()
    {
        $this->fakeLamboConsole();

        $this->instance(ExecutableFinder::class, Mockery::mock(ExecutableFinder::class, function ($mock) {
            $mock->shouldReceive('find')
                ->with('dependencyA')
                ->once()
                ->andReturn('/path/to/dependencyA');

            $mock->shouldReceive('find')
                ->with('dependencyB')
                ->once()
                ->andReturn('/path/to/dependencyB');
        }));

        app(VerifyDependencies::class)(['dependencyA', 'dependencyB']);
    }

    /** @test */
    public function it_throws_and_exception_if_a_required_dependency_is_missing_missing()
    {
        $this->fakeLamboConsole();

        $this->instance(ExecutableFinder::class, Mockery::mock(ExecutableFinder::class, function ($mock) {
            $mock->shouldReceive('find')
                ->with('dependencyA')
                ->once()
                ->andReturn('/path/to/dependencyA');

            $mock->shouldReceive('find')
                ->with('missingDependency')
                ->once()
                ->andReturn(null);
        }));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('missingDependency not installed');

        app(VerifyDependencies::class)(['dependencyA', 'missingDependency']);
    }
}
