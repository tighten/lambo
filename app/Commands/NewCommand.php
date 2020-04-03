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
use App\Actions\RunPresets;
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

        app(DisplayLamboWelcome::class)();

        if (! $this->argument('projectName')) {
            app(DisplayHelpScreen::class)();
            exit;
        }

        $this->alert('Creating a Laravel app ' . $this->argument('projectName'));

        app(SetConfig::class)();

        try {
            app(RunPresets::class)();
            exit('Not creating real apps right now.');

            app(VerifyPathAvailable::class)();

            app(VerifyDependencies::class)(['laravel', 'git', 'valet']);

            app(RunLaravelInstaller::class)();

            app(OpenInEditor::class)();

            app(CustomizeDotEnv::class)();

            $this->info(app(CreateDatabase::class)());

            app(GenerateAppKey::class)();

            app(ConfigureFrontendFramework::class)();

            app(InitializeGitRepo::class)();

            app(InstallNpmDependencies::class)();

            app(CompileAssets::class)();

            app(RunAfterScript::class)();

            app(ValetLink::class)();

            app(ValetSecure::class)();

            app(OpenInBrowser::class)();


            $this->info("\nDone. Happy coding!");
        } catch (Exception $e) {
            $this->error("\nFAILURE: " . $e->getMessage());
        }
        // @todo cd into it
    }
}
