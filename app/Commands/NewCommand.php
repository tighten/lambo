<?php

namespace App\Commands;

use App\ActionsOnInstall\ChangeDirectory;
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
use App\ActionsPreInstall\DisplayConfigScreen;
use App\ActionsPreInstall\MergeInlineOptionsToConfig;
use App\ActionsPreInstall\RunVerifications;
use App\Facades\OptionManager;
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
        {--c|custom : Customise config options.}
        {--dev : Choose the dev branch instead of master.}
        {--editor= : The editor command. Use false for none.}
        {--commit= : Set the first commit message.}
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
        config()->set('lambo.store.install', true);

        $this->action(MergeInlineOptionsToConfig::class);
        $this->action(RunVerifications::class);

        // TODO Load `config` and `after` files, overloading existing.

        OptionManager::loadOptions($this);

        if ($this->option('custom')) {
            $this->customizeConfig();

            // TODO ConfigOptionItems only appear if verification possible eg: having 6 editors possible, but user just has 3
            // TODO remove config: lambo.store, replace with a Singleton (with state).
            // TODO Maybe lambo.config, may flatten folder to config/lambo.php
        }

        if (! config('lambo.store.install')) {
            return;
        }

        // TODO Run verifications that we only know as ConfigOptions. Example: yarn/npm/editor available
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
        $this->action(ChangeDirectory::class);
    }

    /**
     * Displays the initial screen.
     *
     * @param null|string $message
     * @param string $level
     * @return void
     */
    public function customizeConfig(?string $message = null, ?string $level = 'info'): void
    {
        $configScreen = app(DisplayConfigScreen::class, ['console' => $this]);

        $configScreen($message, $level);
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
