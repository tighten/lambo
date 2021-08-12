<?php

namespace Tests\Feature;

use App\Actions\PushToGitHub;
use App\ConsoleWriter;
use App\Shell;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

/**
 * @group git-and-github
 */
class PushToGitHubTest extends TestCase
{
    private $consoleWriter;

    /** @test */
    function it_pushes_to_github()
    {
        config(['lambo.store.push_to_github' => true]);

        $this->shell->shouldReceive('execInProject')
            ->with('git rev-parse --abbrev-ref HEAD')
            ->once()
            ->andReturn(FakeProcess::success()->withOutput('main'))
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with('git push -u origin main')
            ->once()
            ->andReturn(FakeProcess::success());

        app(PushToGitHub::class)();
    }

    /** @test */
    function it_logs_a_warning_if_branch_name_cannot_be_determined()
    {
        $this->consoleWriter = $this->mock(ConsoleWriter::class);

        config(['lambo.store.push_to_github' => true]);

        $this->shouldLogStep('Pushing new project to GitHub');

        $getBranchNameCommand = 'git rev-parse --abbrev-ref HEAD';
        $errorMessage = 'Oops, something went wrong.';
        $failedBranchNameProcess = FakeProcess::fail($getBranchNameCommand)
            ->withErrorOutput($errorMessage);

        $this->shell->shouldReceive('execInProject')
            ->with($getBranchNameCommand)
            ->once()
            ->andReturn($failedBranchNameProcess)
            ->globally()
            ->ordered();

        $this->shouldLogWarning(PushToGitHub::WARNING_UNABLE_TO_GET_BRANCH_NAME);
        $this->shouldLogWarning("Failed to run {$getBranchNameCommand}");
        $this->shouldShowOutputErrors($errorMessage);

        app(PushToGitHub::class)();
    }

    /** @test */
    function it_logs_a_warning_if_pushing_to_git_hub_fails()
    {
        $this->consoleWriter = $this->mock(ConsoleWriter::class);

        config(['lambo.store.push_to_github' => true]);

        $this->shouldLogStep('Pushing new project to GitHub');

        $branchNameProcess = FakeProcess::success()->withOutput('main');
        $this->shell->shouldReceive('execInProject')
            ->with('git rev-parse --abbrev-ref HEAD')
            ->once()
            ->andReturn($branchNameProcess)
            ->globally()
            ->ordered();

        $pushToGitHubCommand = "git push -u origin {$branchNameProcess->getOutput()}";
        $errorMessage = 'Oops, something went wrong.';
        $failedPushToGitHubProcess = FakeProcess::fail($pushToGitHubCommand)
            ->withErrorOutput($errorMessage);

        $this->shell->shouldReceive('execInProject')
            ->with($pushToGitHubCommand)
            ->once()
            ->andReturn($failedPushToGitHubProcess)
            ->globally()
            ->ordered();

        $this->shouldLogWarning(PushToGitHub::WARNING_FAILED_TO_PUSH);
        $this->shouldLogWarning("Failed to run {$pushToGitHubCommand}");
        $this->shouldShowOutputErrors($errorMessage);

        app(PushToGitHub::class)();
    }

    /** @test */
    function it_skips_pushing_to_github()
    {
        $shell = $this->spy(Shell::class);

        $pushCommand = 'git push -u origin ';

        config(['lambo.store.push_to_github' => null]);
        app(PushToGitHub::class)();
        $shell->shouldNotHaveReceived('execInProject', [$pushCommand]);

        config(['lambo.store.push_to_github' => false]);
        app(PushToGitHub::class)();
        $shell->shouldNotHaveReceived('execInProject', [$pushCommand]);
    }

    private function shouldLogWarning(string $warning): void
    {
        $this->consoleWriter->shouldReceive('warn')
            ->with($warning)
            ->globally()
            ->ordered();
    }

    private function shouldLogStep(string $step)
    {
        $this->consoleWriter->shouldReceive('logStep')
            ->with($step)
            ->globally()
            ->ordered();
    }

    private function shouldShowOutputErrors(string $error)
    {
        $this->consoleWriter->shouldReceive('showOutputErrors')
            ->with($error)
            ->globally()
            ->ordered();
    }
}
