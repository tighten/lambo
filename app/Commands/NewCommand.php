<?php

namespace App\Commands;

use App\Support\ShellCommand;
use App\Actions\InitializeGit;
use App\Actions\RunVerifications;
use App\Actions\DisplayLamboLogo;
use App\Actions\MergeOptionsToConfig;
use App\Actions\CreateNewApplication;
use App\Services\AfterCommandsService;
use App\Actions\SetupLamboStoreConfigs;
use App\Actions\PromptForCustomization;
use App\Services\CreateDatabaseService;
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
        {--database= : Create a database with Project Name. Options: false, mysql, sqlite}
        ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a fresh Laravel application';

    public $projectName;

    public $currentWorkingDir;

    /**
     * @var ShellCommand
     */
    private $shellCommand;

    /**
     * LaravelNewCommand constructor.
     *
     * @param ShellCommand $shellCommand
     */
    public function __construct(ShellCommand $shellCommand)
    {
        parent::__construct();

        $this->currentWorkingDir = getcwd();

        $this->shellCommand = $shellCommand;
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
         * Base config is loaded and merged with ~/.lambo/config.php in the \App\Providers\AppServiceProvider
         * And then gets overridden here with existing inline options
         *
         */
        $this->action(MergeOptionsToConfig::class);

        $this->action(DisplayCurrentConfiguration::class);

        $this->action(PromptForCustomization::class);

        $this->action(SetupLamboStoreConfigs::class);

        $this->action(CreateNewApplication::class);

        $this->action(InitializeGit::class);

//        $this->createDatabase();

        $this->afterCommands();

        $this->shellCommand->inDirectory($dir = $this->projectName, 'valet open');
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
     * Create the database, if opted-in
     */
    public function createDatabase(): void
    {
        if (config('lambo.create_database')) {
            app()->make(CreateDatabaseService::class, ['console' => $this])->handle();
        }
    }

    /**
     * Run the after commands
     */
    protected function afterCommands(): void
    {
        resolve(AfterCommandsService::class)->handle();
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
