<?php

namespace Tests\Feature;

use App\ActionsPreInstall\PromptForCustomization;
use App\Support\ShellCommand;
use Tests\TestCase;

class InitialConfigurationsTest extends TestCase
{
    /** @test */
    public function projectNameSetCorrectly(): void
    {
        /** @var \Mockery\MockInterface $shellCommand */
        $shellCommand = $this->mock(ShellCommand::class);

        $shellCommand->shouldReceive('inDirectory');

        $this->artisan('new', ['projectName' => 'blog'])
            ->expectsQuestion(PromptForCustomization::CUSTOMIZE_QUESTION, 'r')
            ->assertExitCode(0);

        $this->assertEquals('blog', config('lambo.store.project_name'));
    }
}
