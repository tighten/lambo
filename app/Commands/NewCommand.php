<?php

namespace App\Commands;

use App\Actions\ConfigureFrontendFramework;
use App\Actions\CreateDatabase;
use App\Actions\CustomizeDotEnv;
use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayLamboWelcome;
use App\Actions\GenerateAppKey;
use App\Actions\InitializeGitRepo;
use App\Actions\MigrateDatabase;
use App\Actions\OpenInBrowser;
use App\Actions\OpenInEditor;
use App\Actions\RunAfterScript;
use App\Actions\RunLaravelInstaller;
use App\Actions\SavedConfig;
use App\Actions\UpgradeSavedConfiguration;
use App\Actions\ValetLink;
use App\Actions\ValetSecure;
use App\Actions\ValidateConfiguration;
use App\Actions\VerifyDependencies;
use App\Actions\VerifyPathAvailable;
use App\Configuration\CommandLineConfiguration;
use App\Configuration\LamboConfiguration;
use App\Configuration\SavedConfiguration;
use App\Configuration\SetConfig;
use App\Configuration\ShellConfiguration;
use App\LamboException;
use App\Options;

class NewCommand extends LamboCommand
{
    use Debug;

    protected $signature;
    protected $description = 'Creates a fresh Laravel application';

    public function __construct()
    {
        $this->signature = $this->buildSignature();

        parent::__construct();

        app()->bind('console', function () {
            return $this;
        });
    }

    public function buildSignature()
    {
        return collect((new Options)->all())->reduce(
            function ($carry, $option)
            {
                return $carry . $this->buildSignatureOption($option);
            },
            "new\n{projectName? : Name of the Laravel project}"
        );
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
        app(DisplayLamboWelcome::class)();

        if (! $this->argument('projectName')) {
            app(DisplayHelpScreen::class)();
            exit;
        }

        $this->setConfig();

        if (app(UpgradeSavedConfiguration::class)()) {
            app('console-writer')->newLine();
            app('console-writer')->note('Your Lambo configuration (~/.lambo/config) has been updated.');
            app('console-writer')->note('Please review the changes then run lambo again.');
            if ($this->confirm(sprintf("Review the changes now in %s?", config('lambo.store.editor')))) {
                app(SavedConfig::class)->createOrEditConfigFile("config");
            }
            return;
        }

        app('console-writer')->sectionTitle("Creating a new Laravel app '{$this->argument('projectName')}'");

        try {
            app(ValidateConfiguration::class)();
            app(VerifyPathAvailable::class)();
            app(VerifyDependencies::class)();
            app(RunLaravelInstaller::class)();
            app(CustomizeDotEnv::class)();
            app(GenerateAppKey::class)();
            app(CreateDatabase::class)();
            app(ConfigureFrontendFramework::class)();
            app(MigrateDatabase::class)();
            app(InitializeGitRepo::class)();
            app(RunAfterScript::class)();
            app(ValetLink::class)();
            app(ValetSecure::class)();
            app(OpenInEditor::class)();
            app(OpenInBrowser::class)();
        } catch (LamboException $e) {
            app('console-writer')->exception($e->getMessage());
            exit;
        }

        app('console-writer')->newLine();
        app('console-writer')->text([
            '<fg=green>Done, happy coding!</>',
            'Lambo is brought to you by the lovely folks at <fg=blue;href=https://tighten.co/>Tighten</>.',
        ]);
        app('console-writer')->newLine();
    }

    private function setConfig(): void
    {
        config(['lambo.store' => []]); // @todo remove if debug code is removed.

        $commandLineConfiguration = new CommandLineConfiguration([
            'editor' => LamboConfiguration::EDITOR,
            'message' => LamboConfiguration::COMMIT_MESSAGE,
            'path' => LamboConfiguration::ROOT_PATH,
            'browser' => LamboConfiguration::BROWSER,
            'frontend' => LamboConfiguration::FRONTEND_FRAMEWORK,
            'dbhost' => LamboConfiguration::DATABASE_HOST,
            'dbport' => LamboConfiguration::DATABASE_PORT,
            'dbname' => LamboConfiguration::DATABASE_NAME,
            'dbuser' => LamboConfiguration::DATABASE_USERNAME,
            'dbpassword' => LamboConfiguration::DATABASE_PASSWORD,
            'create-db' => LamboConfiguration::CREATE_DATABASE,
            'migrate-db' => LamboConfiguration::MIGRATE_DATABASE,
            'link' => LamboConfiguration::VALET_LINK,
            'secure' => LamboConfiguration::VALET_SECURE,
            'with-output' => LamboConfiguration::WITH_OUTPUT,
            'dev' => LamboConfiguration::USE_DEVELOP_BRANCH,
            'full' => LamboConfiguration::FULL,
            'teams' => LamboConfiguration::TEAMS,
            'inertia' => LamboConfiguration::INERTIA,
            'livewire' => LamboConfiguration::LIVEWIRE,
            'projectName' => LamboConfiguration::PROJECT_NAME,
        ]);

        $savedConfiguration = new SavedConfiguration([
            'PROJECTPATH' => LamboConfiguration::ROOT_PATH,
            'MESSAGE' => LamboConfiguration::COMMIT_MESSAGE,
            'DEVELOP' => LamboConfiguration::USE_DEVELOP_BRANCH,
            'CODEEDITOR' => LamboConfiguration::EDITOR,
            'BROWSER' => LamboConfiguration::BROWSER,
            'DB_HOST' => LamboConfiguration::DATABASE_HOST,
            'DB_PORT' => LamboConfiguration::DATABASE_PORT,
            'DB_NAME' => LamboConfiguration::DATABASE_NAME,
            'DB_USERNAME' => LamboConfiguration::DATABASE_USERNAME,
            'DB_PASSWORD' => LamboConfiguration::DATABASE_PASSWORD,
            'CREATE_DATABASE' => LamboConfiguration::CREATE_DATABASE,
            'MIGRATE_DATABASE' => LamboConfiguration::MIGRATE_DATABASE,
            'LINK' => LamboConfiguration::VALET_LINK,
            'SECURE' => LamboConfiguration::VALET_SECURE,
        ]);

        $shellConfiguration = new ShellConfiguration([
            'EDITOR' => LamboConfiguration::EDITOR,
        ]);

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration
        ))([
            LamboConfiguration::COMMAND => self::class,
            LamboConfiguration::EDITOR => 'nano',
            LamboConfiguration::COMMIT_MESSAGE => 'Initial commit',
            LamboConfiguration::ROOT_PATH => getcwd(),
            LamboConfiguration::BROWSER => null,
            LamboConfiguration::DATABASE_HOST => '127.0.0.1',
            LamboConfiguration::DATABASE_PORT => 3306,
            LamboConfiguration::DATABASE_NAME => $this->argument('projectName'),
            LamboConfiguration::DATABASE_USERNAME => 'root',
            LamboConfiguration::DATABASE_PASSWORD => '',
            LamboConfiguration::CREATE_DATABASE => false,
            LamboConfiguration::MIGRATE_DATABASE => false,
            LamboConfiguration::VALET_LINK => false,
            LamboConfiguration::VALET_SECURE => false,
            LamboConfiguration::WITH_OUTPUT => false,
            LamboConfiguration::USE_DEVELOP_BRANCH => false,
            LamboConfiguration::FULL => false,
            LamboConfiguration::INERTIA => false,
            LamboConfiguration::LIVEWIRE => false,
            LamboConfiguration::TEAMS => false,
            LamboConfiguration::PROJECT_NAME => null,
            LamboConfiguration::TLD => null,
        ]);

        if (app('console-writer')->isDebug()) {
            $this->debugReport();
        }
    }
}
