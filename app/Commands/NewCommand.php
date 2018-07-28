<?php

namespace App\Commands;

use App\Services\DisplayService;
use function is_bool;
use function is_string;
use App\Support\ShellCommand;
use App\Services\QuestionsService;
use App\Services\VerificationService;
use App\Services\AfterCommandsService;
use App\Services\CreateDatabaseService;
use Illuminate\Console\Scheduling\Schedule;
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
        {--dev : Choose the dev branch instead of master}
        ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a fresh Laravel application';

    public $projectName;

    public $dev;

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

        $this->shellCommand = $shellCommand;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->displayLamboLogo();

        $this->runVerifications();

        $this->showConfigs();

        $this->promptForCustomization();

        /**
         * TODO on setup, merge config with loaded ~/.lambo/config and interactive options
         */
        $this->setUp();

        $this->installNewApplication();

        $this->initializeGit();

//        $this->createDatabase();

        $this->afterCommands();

        $this->shellCommand->inDirectory($dir = $this->projectName, 'valet open');
    }

    /**
     * Run the verifications service
     *
     */
    protected function runVerifications(): void
    {
        resolve(VerificationService::class)->handle($this);
    }

    /**
     * Setup
     *
     */
    protected function setUp(): void
    {
        $this->projectName = $this->argument('projectName');
        $this->dev = $this->option('dev');
    }

    /**
     * The "laravel new" command, and wether if should require the dev branch or not
     *
     */
    protected function installNewApplication(): void
    {
        if ($this->dev) {
            $this->info('Creating application from dev branch.');
            $this->shellCommand->inCurrentWorkingDir("laravel new {$this->projectName} --dev");
        } else {
            $this->info('Creating application from release branch.');
            $this->shellCommand->inCurrentWorkingDir("laravel new {$this->projectName}");
        }
    }

    /**
     * Initialize Git
     *
     */
    protected function initializeGit(): void
    {
        $showOutput = false;

        $this->shellCommand->inDirectory($this->projectName, 'git init', $showOutput);
        $this->shellCommand->inDirectory($this->projectName, 'git add .', $showOutput);
        $this->shellCommand->inDirectory($this->projectName, 'git commit -m "Initial commit"', $showOutput);

        $this->info('Git repository initialized.');
    }

    /**
     * Questions or defaults
     */
    public function questions(): void
    {
        app()->make(QuestionsService::class)->handle($this);
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
     * Show current config
     */
    protected function showConfigs(): void
    {
        $rows = config('lambo', []);

        $rows = collect($rows)->filter(function ($item, $key) {
            return $key !== 'after';
        })->map(function ($item, $key) {

            if (is_bool($item)) {
                $item = $item ? 'true' : 'false';
            }

            if (is_string($item) && $item === '') {
                $item = '(empty)';
            }

            return [
                'Configuration' => $key,
                'Value' => $item,
            ];
        })->all();

        $this->table(['Configuration', 'Value'], $rows);
    }

    /**
     * Display Lambo Logo
     *
     */
    protected function displayLamboLogo(): void
    {
        app()->make(DisplayService::class,['console' => $this])->displayLamboLogo();
    }

    /**
     * Prompt for customization
     */
    public function promptForCustomization()
    {
        $customizeQuestion = 'Would you like to (R)un with current config, or (C)ustomize?';
        $answer = false;
        while(!collect(['c','C','r','R'])->contains($answer))
        {
            $answer = $this->ask($customizeQuestion);
            if (collect(['c','C'])->contains($answer)) {
                $this->questions();
            }
        }
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
