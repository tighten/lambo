<?php

namespace Tests\Feature;

use App\ActionsPreInstall\CustomiseConfigRuntime;
use App\ActionsPreInstall\PromptForCustomization;
use App\Support\ShellCommand;
use Tests\TestCase;

class InitialConfigurationsTest extends TestCase
{
    /** @test */
    public function projectNameSetCorrectly(): void
    {
        /** @var \Mockery\MockInterface $shellCommand */
        $shellCommand = $this->spy(ShellCommand::class);

        $this->artisan('new', ['projectName' => 'blog'])
            ->run();

        $shellCommand->shouldHaveReceived('inDirectory')
            ->withArgs([
                config('lambo.store.install_path'),
                'laravel new blog'
            ]);

        $this->assertEquals('blog', config('lambo.store.project_name'));
    }

    /** @test */
    public function canInitConfigurationAndLookAtConfigsAndRunInstallation(): void
    {
        /** @var \Mockery\MockInterface $shellCommand */
        $shellCommand = $this->spy(ShellCommand::class);

        $this->artisan('new', [
            'projectName' => 'blog',
            '--custom' => true,
        ])
            ->expectsQuestion(PromptForCustomization::CUSTOMISE_QUESTION, 'r')
            ->assertExitCode(0)
            ->run();

        $shellCommand->shouldHaveReceived('inDirectory')
            ->withArgs([
                config('lambo.store.install_path'),
                'laravel new blog'
            ]);

        $this->assertEquals('blog', config('lambo.store.project_name'));
    }

    /** @test */
    public function canInitConfigurationAndCustomizeAndRunExit(): void
    {
        /** @var \Mockery\MockInterface $shellCommand */
        $shellCommand = $this->spy(ShellCommand::class);

        $this->artisan('new', [
            'projectName' => 'blog',
            '--custom' => true,
        ])
            ->expectsQuestion(PromptForCustomization::CUSTOMISE_QUESTION, 'c')
            ->expectsQuestion('Which configuration to setup?', CustomiseConfigRuntime::EXIT_MESSAGE)
            ->expectsQuestion(PromptForCustomization::CUSTOMISE_QUESTION, 'e')
            ->assertExitCode(0)
            ->run();

        $shellCommand->shouldNotHaveReceived('inDirectory');
    }

    /** @test */
    public function canInitConfigurationAndLookAtConfigsAndLeave(): void
    {
        /** @var \Mockery\MockInterface $shellCommand */
        $shellCommand = $this->spy(ShellCommand::class);

        $this->artisan('new', [
                'projectName' => 'blog',
                '--custom' => true,
            ])
            ->expectsQuestion(PromptForCustomization::CUSTOMISE_QUESTION, 'e')
            ->assertExitCode(0);

        $shellCommand->shouldNotHaveReceived('inDirectory');
    }
}
