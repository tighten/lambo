<?php

namespace App\Commands;

use App\Actions\MakeAuth;
use App\Actions\ValetLink;
use App\Actions\OpenEditor;
use App\Actions\OpenBrowser;
use App\Actions\InitializeGit;
use App\Actions\AfterCommands;
use App\Actions\CreateDatabase;
use App\Actions\RunVerifications;
use App\Actions\DisplayLamboLogo;
use App\Actions\UpdateDotEnvFile;
use App\Actions\MergeOptionsToConfig;
use App\Actions\CreateNewApplication;
use App\Actions\SetupLamboStoreConfigs;
use App\Actions\PromptForCustomization;
use App\Actions\InstallNodeDependencies;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Actions\DisplayCurrentConfiguration;

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
        $this->action(DisplayLamboLogo::class);
        $this->action(RunVerifications::class);
        /**
         * Base config is loaded and merged with ~/.lambo/config.php in \App\Providers\AppServiceProvider
         * and then gets overridden here with existing inline options
         *
         */
        $this->action(MergeOptionsToConfig::class);
        $this->action(DisplayCurrentConfiguration::class);
        $this->action(PromptForCustomization::class);
        $this->action(SetupLamboStoreConfigs::class);
        $this->action(CreateNewApplication::class);
        $this->action(InitializeGit::class);
        $this->action(CreateDatabase::class);
        $this->action(UpdateDotEnvFile::class);
        $this->action(MakeAuth::class);
        $this->action(AfterCommands::class);
        $this->action(InstallNodeDependencies::class);
        $this->action(ValetLink::class);
        $this->action(OpenBrowser::class);
        $this->action(OpenEditor::class);
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

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
