<?php

namespace Tests\Feature;

use App\Actions\VerifyDependencies;
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
        $this->executableFinder->shouldReceive('find')
            ->with('cmdA')
            ->andReturn('/path/to/cmdA');

        $this->executableFinder->shouldReceive('find')
            ->with('cmdB')
            ->andReturn('/path/to/cmdB');

        app(VerifyDependencies::class)([
            'Command A' => 'cmdA|cmda.example.com',
            'Command B' => 'cmdB|cmdb.example.com',
        ]);
    }

    /** @test */
    function it_throws_a_lambo_exception_if_a_required_dependency_is_missing_missing()
    {
        $this->executableFinder->shouldReceive('find')
            ->with('cmdA')
            ->andReturn('/path/to/cmdA');

        $this->executableFinder->shouldReceive('find')
            ->with('cmdB')
            ->andReturnNull();

        $this->expectException(LamboException::class);

        app(VerifyDependencies::class)([
            'Command A' => 'cmdA|cmda.example.com',
            'Command B' => 'cmdB|cmdb.example.com',
        ]);
    }
}
