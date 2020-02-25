<?php

namespace Tests\Feature;

use App\Actions\VerifyDependencies;
use Exception;
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
        $this->executableFinder->shouldReceive('find')
            ->with('dependencyA')
            ->once()
            ->andReturn('/path/to/dependencyA');

        $this->executableFinder->shouldReceive('find')
            ->with('dependencyB')
            ->once()
            ->andReturn('/path/to/dependencyB');

        app(VerifyDependencies::class)(['dependencyA', 'dependencyB']);
    }

    /** @test */
    function it_throws_and_exception_if_a_required_dependency_is_missing_missing()
    {
        $this->executableFinder->shouldReceive('find')
            ->with('dependencyA')
            ->once()
            ->andReturn('/path/to/dependencyA');

        $this->executableFinder->shouldReceive('find')
            ->with('missingDependency')
            ->once()
            ->andReturn(null);

        $this->expectException(Exception::class);

        app(VerifyDependencies::class)(['dependencyA', 'missingDependency']);
    }
}
