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
use App\Actions\ValetLink;
use App\Actions\ValetSecure;
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
    }

    public function buildSignature()
    {
        return collect((new Options)->all())->reduce(
            function (
                $carry,
                $option
            ) {
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
        parent::handle();

        app(DisplayLamboWelcome::class)();

        if (! $this->argument('projectName')) {
            app(DisplayHelpScreen::class)();
            exit;
        }

        $this->setConfig();

        app('console-writer')->ignoreVerbosity()->note(sprintf('Creating a Laravel app "%s"', $this->argument('projectName')));

        try {
            app(VerifyPathAvailable::class)();
            app(VerifyDependencies::class)([
                'The Laravel installer' => 'laravel|https://laravel.com/docs/installation#installing-laravel',
                'Laravel valet'         => 'valet|https://laravel.com/docs/valet',
                'Git version control'   => 'git|https://git-scm.com/',
            ]);
            app(RunLaravelInstaller::class)();
            app(OpenInEditor::class)();
            app(CustomizeDotEnv::class)();
            app(CreateDatabase::class)();
            app(GenerateAppKey::class)();
            app(ConfigureFrontendFramework::class)(['bootstrap', 'react', 'vue']);
            app(InitializeGitRepo::class)();
            app(InstallNpmDependencies::class)();
            app(CompileAssets::class)();
            app(RunAfterScript::class)();
            app(ValetLink::class)();
            app(ValetSecure::class)();
            app(OpenInBrowser::class)();
        } catch (LamboException $e) {
            app('console-writer')->exception($e->getMessage());

            return;
        }

        app('console-writer')->ignoreVerbosity()->newLine();
        app('console-writer')->ignoreVerbosity()->text([
            '<fg=green>Done, happy coding!</>',
            'Lambo is bought to you by the lovely folks at <fg=blue;href=https://tighten.co/>Tighten</>.',
        ]);
        app('console-writer')->ignoreVerbosity()->newLine();
    }

    private function setConfig(): void
    {
        config(['lambo.store' => []]); // @todo remove if debug code is removed.

        $commandLineConfiguration = new CommandLineConfiguration([
            'editor'      => LamboConfiguration::EDITOR,
            'message'     => LamboConfiguration::COMMIT_MESSAGE,
            'path'        => LamboConfiguration::ROOT_PATH,
            'browser'     => LamboConfiguration::BROWSER,
            'frontend'    => LamboConfiguration::FRONTEND_FRAMEWORK,
            'dbname'      => LamboConfiguration::DATABASE_NAME,
            'dbuser'      => LamboConfiguration::DATABASE_USERNAME,
            'dbpassword'  => LamboConfiguration::DATABASE_PASSWORD,
            'create-db'   => LamboConfiguration::CREATE_DATABASE,
            'auth'        => LamboConfiguration::AUTH,
            'node'        => LamboConfiguration::NODE,
            'mix'         => LamboConfiguration::MIX,
            'link'        => LamboConfiguration::VALET_LINK,
            'secure'      => LamboConfiguration::VALET_SECURE,
            'with-output' => LamboConfiguration::WITH_OUTPUT,
            'dev'         => LamboConfiguration::USE_DEVELOP_BRANCH,
            'full'        => LamboConfiguration::FULL,
            'no-editor'   => LamboConfiguration::NO_EDITOR,
            'projectName' => LamboConfiguration::PROJECT_NAME,
        ]);

        $savedConfiguration = new SavedConfiguration([
            'CODEEDITOR'      => LamboConfiguration::EDITOR,
            'MESSAGE'         => LamboConfiguration::COMMIT_MESSAGE,
            'PROJECTPATH'     => LamboConfiguration::ROOT_PATH,
            'BROWSER'         => LamboConfiguration::BROWSER,
            'FRONTEND'        => LamboConfiguration::FRONTEND_FRAMEWORK,
            'DB_NAME'         => LamboConfiguration::DATABASE_NAME,
            'DB_USERNAME'     => LamboConfiguration::DATABASE_USERNAME,
            'DB_PASSWORD'     => LamboConfiguration::DATABASE_PASSWORD,
            'CREATE_DATABASE' => LamboConfiguration::CREATE_DATABASE,
            'AUTH'            => LamboConfiguration::AUTH,
            'NODE'            => LamboConfiguration::NODE,
            'MIX'             => LamboConfiguration::MIX,
            'LINK'            => LamboConfiguration::VALET_LINK,
            'SECURE'          => LamboConfiguration::VALET_SECURE,
            'WITH_OUTPUT'     => LamboConfiguration::WITH_OUTPUT,
            'DEVELOP'         => LamboConfiguration::USE_DEVELOP_BRANCH,
            'NO_EDITOR'       => LamboConfiguration::NO_EDITOR,
        ]);

        $shellConfiguration = new ShellConfiguration([
            'EDITOR' => LamboConfiguration::EDITOR,
        ]);

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration
        ))([
            LamboConfiguration::EDITOR             => 'nano',
            LamboConfiguration::COMMIT_MESSAGE     => 'Initial commit',
            LamboConfiguration::ROOT_PATH          => getcwd(),
            LamboConfiguration::BROWSER            => null,
            LamboConfiguration::FRONTEND_FRAMEWORK => null,
            LamboConfiguration::DATABASE_NAME      => $this->argument('projectName'),
            LamboConfiguration::DATABASE_USERNAME  => 'root',
            LamboConfiguration::DATABASE_PASSWORD  => '',
            LamboConfiguration::CREATE_DATABASE    => false,
            LamboConfiguration::AUTH               => false,
            LamboConfiguration::NODE               => false,
            LamboConfiguration::MIX                => false,
            LamboConfiguration::VALET_LINK         => false,
            LamboConfiguration::VALET_SECURE       => false,
            LamboConfiguration::WITH_OUTPUT        => false,
            LamboConfiguration::USE_DEVELOP_BRANCH => false,
            LamboConfiguration::FULL               => false,
            LamboConfiguration::NO_EDITOR          => false,
            LamboConfiguration::PROJECT_NAME       => null,
            LamboConfiguration::TLD                => null,
        ]);

        if (app('console-writer')->isDebug()) {
            $this->debugReport();
        }
    }
}
