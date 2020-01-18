<?php

namespace App\Commands;

use App\Actions\CompileAssets;
use App\Actions\ConfigureFrontendFramework;
use App\Actions\CreateDatabase;
use App\Actions\CustomizeDotEnv;
use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayLamboWelcome;
use App\Actions\GenerateAppKey;
use App\Actions\InitializeGitRepo;
use App\Actions\InstallNpmDependencies;
use App\Actions\OpenInBrowser;
use App\Actions\OpenInEditor;
use App\Actions\RunAfterScript;
use App\Actions\RunLaravelInstaller;
use App\Actions\SetConfig;
use App\Actions\ValetLink;
use App\Actions\ValetSecure;
use App\Actions\VerifyDependencies;
use App\Actions\VerifyPathAvailable;
use App\Options;
use Exception;
use LaravelZero\Framework\Commands\Command;

class NewCommand extends Command
{
    protected $signature;
    protected $description = 'Creates a fresh Laravel application';

    public function __construct()
    {
        $this->signature = $this->buildSignature();

        parent::__construct();
    }

    public function buildSignature()
    {
        return collect((new Options)->all())->reduce(function ($carry, $option) {
            return $carry . $this->buildSignatureOption($option);
        }, "new\n{projectName? : Name of the Laravel project}");
    }

    public function buildSignatureOption($option)
    {
        $call = isset($option['short']) ? ($option['short'] . '|' . $option['long']) : $option['long'];

        if (isset($option['param_description'])) {
            $call .= '=';
        }

        return "\n{--{$call} : {$option['cli_description']}}";
    }

    public function handle()
    {
        app()->bind('console', function () {
            return $this;
        });

        app(SetConfig::class)();
        app(DisplayLamboWelcome::class)();

        if (! $this->argument('projectName')) {
            app(DisplayHelpScreen::class)();
            exit;
        }

        $this->alert('Creating a Laravel app ' . $this->argument('projectName'));

        try {
            $this->logStep('Verifying Path Availability');
            app(VerifyPathAvailable::class)();

            $this->logStep('Verifying Dependencies');
            app(VerifyDependencies::class)();

            $this->logStep('Running the Laravel Installer');
            app(RunLaravelInstaller::class)();

            $this->logStep('Opening In Editor');
            app(OpenInEditor::class)();

            $this->logStep('Customizing .env and .env.example');
            app(CustomizeDotEnv::class)();

            $this->logStep('Creating database if selected');
            app(CreateDatabase::class)();

            $this->logStep('Running php artisan key:generate');
            app(GenerateAppKey::class)();

            $this->logStep('Configuring frontend preset');
            app(ConfigureFrontendFramework::class)();

            $this->logStep('Initializing Git Repo');
            app(InitializeGitRepo::class)();

            $this->logStep('Installing NPM dependencies');
            app(InstallNpmDependencies::class)();

            $this->logStep('Compiling project assets');
            app(CompileAssets::class)();

            $this->logStep('Running after script');
            app(RunAfterScript::class)();

            $this->logStep('Running valet link');
            app(ValetLink::class)();

            $this->logStep('Running valet secure');
            app(ValetSecure::class)();

            $this->logStep('Opening in Browser');
            app(OpenInBrowser::class)();
        } catch (Exception $e) {
            $this->error("\nFAILURE RUNNING COMMAND:");
            $this->error($e->getMessage());
        }

        // @todo cd into it
    }

    public function logStep($step)
    {
        if ($this->option('verbose')) {
            $this->comment("{$step}...\n");
        }
    }
}
