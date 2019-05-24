<?php

namespace Tests\Feature;

use App\ActionsPreInstall\CustomiseConfigRuntime;
use App\ActionsPreInstall\PromptForCustomization;
use App\Facades\OptionManager;
use App\Options\DevBranch;
use App\Support\ShellCommand;
use Tests\TestCase;

class DevBranchOptionTest extends TestCase
{
    /** @test */
    public function canSetDevBranchToTrueOnLaunch(): void
    {
        $this->artisan('new', [
            'projectName' => 'blog',
            '--dev' => true,
        ])
            ->assertExitCode(0)
            ->run();

        $dev = OptionManager::getOption('dev')->getOptionValue();

        $this->assertEquals(true, $dev);

        $this->shellCommand->shouldHaveReceived('inDirectory')
            ->withArgs([
                config('lambo.store.install_path'),
                'laravel new blog --dev'
            ]);
    }

    /** @test */
    public function canSetDevBranchToFalseOnLaunch(): void
    {
        $this->artisan('new', [
                'projectName' => 'blog',
                '--dev' => false,
            ])
            ->assertExitCode(0)
            ->run();

        $dev = OptionManager::getOption('dev')->getOptionValue();

        $this->assertEquals(false, $dev);

        $this->shellCommand->shouldHaveReceived('inDirectory')
            ->withArgs([
                config('lambo.store.install_path'),
                'laravel new blog'
            ]);
    }

    /** @test */
    public function canChooseDevBranchInteractively(): void
    {
        $devBranchOption = new DevBranch();

        $this->artisan('new', [
                'projectName' => 'blog',
                '--custom' => true,
            ])
            ->expectsQuestion(PromptForCustomization::CUSTOMISE_QUESTION, 'c')
            ->expectsQuestion(CustomiseConfigRuntime::CUSTOMISATION_QUESTION, $devBranchOption->getTitle())
            ->expectsQuestion($devBranchOption->displayDescription(), 'No')
            ->expectsQuestion(PromptForCustomization::CUSTOMISE_QUESTION, 'r')
            ->assertExitCode(0)
            ->run();

        $dev = OptionManager::getOption('dev')->getOptionValue();

        $this->assertEquals(false, $dev);

        $this->artisan('new', [
            'projectName' => 'blog',
            '--custom' => true,
        ])
            ->expectsQuestion(PromptForCustomization::CUSTOMISE_QUESTION, 'c')
            ->expectsQuestion(CustomiseConfigRuntime::CUSTOMISATION_QUESTION, $devBranchOption->getTitle())
            ->expectsQuestion($devBranchOption->displayDescription(), 'Yes')
            ->expectsQuestion(PromptForCustomization::CUSTOMISE_QUESTION, 'r')
            ->assertExitCode(0)
            ->run();

        $dev = OptionManager::getOption('dev')->getOptionValue();

        $this->assertEquals(true, $dev);
    }
}
