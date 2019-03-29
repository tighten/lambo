<?php

namespace App\Commands;

use App\ActionsOnInstall\CreateDatabase;
use App\ActionsOnInstall\CreateNewApplication;
use App\ActionsOnInstall\InitializeGit;
use App\ActionsOnInstall\InstallNodeDependencies;
use App\ActionsOnInstall\MakeAuth;
use App\ActionsOnInstall\OpenBrowser;
use App\ActionsOnInstall\OpenEditor;
use App\ActionsOnInstall\SetupLamboStoreConfigs;
use App\ActionsOnInstall\UpdateDotEnvFile;
use App\ActionsOnInstall\ValetLink;
use App\ActionsPreInstall\DisplayLamboLogo;
use App\ActionsPreInstall\MergeInlineOptionsToConfig;
use App\ActionsPreInstall\PromptForCustomization;
use App\ActionsPreInstall\RunVerifications;
use LaravelZero\Framework\Commands\Command;

class NewCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new 
        {projectName : Name of the Laravel project}
        {--dev : Choose the dev branch instead of master.}
        {--editor= : The editor command. Use false for none.}
        {--message= : Set the first commit message.}
        {--path= : Specify where to install the application.}
        {--auth : Use Artisan to scaffold all of the routes and views you need for authentication.}
        {--node= : Run yarn if installed, otherwise runs npm install after creating the project.}
        {--browser= : Define which browser you want to open the project in.}
        {--link : Create a Valet link to the project directory.}
        {--tld= : The top level domain for the local server.}
        {--database= : Create a database with Project Name. Options: false, mysql, sqlite}
        ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a fresh Laravel application';

    public $currentWorkingDir;

    /**
     * LaravelNewCommand constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->currentWorkingDir = getcwd();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->action(MergeInlineOptionsToConfig::class);
        $this->action(RunVerifications::class);

        $this->initialScreen();

        /*
         * It will reach here where (R)un selection is made on the initial screen.
         */

        $this->action(SetupLamboStoreConfigs::class);
        $this->action(CreateNewApplication::class);
        $this->action(InitializeGit::class);
        $this->action(CreateDatabase::class);
        $this->action(UpdateDotEnvFile::class);
        $this->action(MakeAuth::class);
        $this->action(InstallNodeDependencies::class);
        $this->action(ValetLink::class);
        $this->action(OpenBrowser::class);
        $this->action(OpenEditor::class);
    }

    /**
     * Displays the initial screen.
     *
     * @param null|string $message
     * @param string $level
     * @return void
     */
    public function initialScreen(?string $message = null, ?string $level = 'info'): void
    {
        $emptySpace = '';
        foreach (range(1, 15) as $i) {
            $emptySpace .= PHP_EOL;
        }
        $this->info($emptySpace);

        $this->action(DisplayLamboLogo::class);

        if ($message !== null) {
            $this->info('');
            switch ($level) {
                case 'error':
                    $this->error($message);
                    break;
                case 'alert':
                    $this->alert($message);
                    break;
                default:
                    $this->info($message);
            }
        }

        $this->action(PromptForCustomization::class);
    }

    /**
     * Invoke an Action Class
     *
     * @param $actionClass
     */
    public function action($actionClass): void
    {
        app($actionClass, ['console' => $this])();
    }
}
