<?php

namespace App\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Tivie\OS\Detector;

class NewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new
        {name : Name of the Laravel project}
        {--a|auth : Run make:auth}
        {--b|browser= : Browser you want to open the project in}
        {--c|createdb= : Create a database; pass in sqlite or mysql}
        {--d|dev : Choose the dev branch instead of master}
        {--e|editor= : Editor to open the project in}
        {--l|link : Create a Valet link to the project directory}
        {--m|message= : Set the first commit message}
        {--y|node : Set to execute yarn or npm install}
        {--p|path= : Base path for the installation, otherwise CWD is used}';

    protected $projectname = '';

    protected $projectpath = '';

    protected $projecturl = '';

    protected $cwd = '';

    protected $basepath = '';

    protected $editors_terminal = ['vim', 'vi', 'nano', 'pico', 'ed', 'emacs', 'nvim'];

    protected $editors_gui = ['pstorm', 'subl', 'atom', 'textmate', 'geany'];

    protected $editor = '';

    protected $tools = [];

    protected $tld = 'test';

    protected $dbtypes = ['sqlite', 'mysql'];

    protected $os = null;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Laravel application';

    /**
     * Create a new command instance.
     */
    public function __construct(ExecutableFinder $finder, Detector $detector)
    {
        parent::__construct();

        $this->cwd = getcwd();

        $this->os = $detector;
        $this->finder = $finder;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->setup();

        $this->checkForRequiredTools();

        $this->checkForExistingProjectInPath();

        $branch = '';

        if ($this->option('dev')) {
            $this->warn('The laravel installation will use the latest developmental branch by passing in --dev');
            $branch = ' --dev';
        }

        $this->info("\nCreating a new project named {$this->projectname}\n");

        $this->runCommand("laravel new {$this->projectname}{$branch}", $this->basepath);

        $this->info('Updating .env in the new application.');

        if (! $this->replaceEnvVariables()) {
            $this->error('Failure updating .env.');
        }

        if ($this->option('auth')) {
            $this->runCommand('php artisan make:auth');
        }

        if ($this->option('createdb')) {
            $this->chooseDatabase($this->option('createdb'));
        }

        if ($this->option('node')) {
            $this->doNodeOrYarn();
        }

        if ($this->hasTool('valet')) {
            if ($this->option('link')) {
                $this->doValetLink();
            }

            $this->openBrowser();
        }

        if ($this->hasTool('git')) {
            $this->doGit();
        }

        $this->openTextEditor();

        $this->info("You're ready to go! Remember to cd into '{$this->projectpath}' before you start editing.");
    }

    protected function setup()
    {
        $this->projectname = $this->argument('name');

        if ($this->hasTool('valet')) {
            if ($tld = json_decode(File::get($_SERVER['HOME'] . '/.valet/config.json'))->domain) {
                $this->tld = $tld;
            }
        }

        $this->projecturl = 'http://' . $this->projectname . '.' . $this->tld;
        $this->basepath = $this->cwd;

        if ($this->option('path')) {
            $path = $this->option('path');

            if (is_dir($path)) {
                $this->basepath = $path;
            } else {
                $this->warn("Your defined path '{$path}' is not a directory; skipping it and using '{$this->basepath}' instead.");
            }
        }

        $this->projectpath = $this->basepath . DIRECTORY_SEPARATOR . $this->projectname;
    }

    protected function checkForRequiredTools()
    {
        if (! $this->hasTool('laravel')) {
            $this->error('Cannot find Laravel installer; exiting.');

            exit;
        }
    }

    protected function checkForExistingProjectInPath()
    {
        if (is_dir($this->projectpath)) {
            if (! $this->askToAndRemoveProject()) {
                $this->error('Goodbye!');
                exit;
            }
        }
    }

    protected function runCommand($command, $path = null, $iterator = false)
    {
        $path = $path ?: $this->projectpath;
        $this->line("Executing '{$command}' in '{$path}'\n");

        $process = new Process($command);
        $process->setWorkingDirectory($path);

        if (! $iterator) {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->line($process->getOutput());

            return;
        }

        $process->start();

        $iterator = $process->getIterator($process::ITER_SKIP_ERR | $process::ITER_KEEP_OUTPUT);

        foreach ($iterator as $data) {
            $this->line($data);
        }
    }

    protected function runIteratorCommand($command, $path = null)
    {
        return $this->runCommand($command, $path, $iterator = true);
    }

    protected function askToAndRemoveProject()
    {
        $this->info("The directory '{$this->projectpath}' already exists.");

        if (! $this->confirm("Proceed by removing the following directory? {$this->projectpath}")) {
            $this->error("You have chosen to not remove the '{$this->projectpath}' directory; quitting.");

            return false;
        }

        $fs = new Filesystem;

        if (! $fs->deleteDirectory($this->projectpath)) {
            $this->error("I was unable to remove the '{$this->projectpath}' directory so I must exit.");

            return false;
        }

        $this->info("I removed the following directory: {$this->projectpath}");

        return true;
    }

    protected function replaceEnvVariables()
    {
        return $this->updateDotEnv([
            'DB_DATABASE' => $this->projectname,
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
            'APP_URL' => $this->projecturl,
        ]);
    }

    protected function doNodeOrYarn()
    {
        $command = null;

        if ($this->hasTool('yarn')) {
            $command = 'yarn';
        } elseif ($this->hasTool('npm')) {
            $command = 'npm install';
        } else {
            $this->error('Either yarn or npm are required');

            return false;
        }

        $this->runIteratorCommand($command, $this->projectpath);
    }

    protected function chooseDatabase($dbtype)
    {
        if (! in_array($dbtype, $this->dbtypes)) {
            $this->info("You passed in '--createdb '{$dbtype}' but I do not understand");
            $type = $this->anticipate('What type of database would you like to install? Options are ' . implode(' or ', $this->dbtypes) . '.', $this->dbtypes, $this->dbtypes[0]);
        } else {
            $type = $dbtype;
        }

        if (! in_array($type, $this->dbtypes)) {
            $this->alert("Now you're being silly. Entering '{$type}', really? Okay, I won't create a database for you.");
        } else {
            $this->info("I am creating a new {$type} database");

            if ($this->createDatabase($type)) {
                $this->runCommand('php artisan migrate:fresh');
            }
        }
    }

    protected function doValetLink()
    {
        if (! $this->hasTool('valet')) {
            $this->warn('Cannot find Valet on your system; a Valet link was not created.');

            return false;
        }

        $this->runCommand("valet link {$this->projectname}");
    }

    protected function doGit()
    {
        if (! $this->hasTool('git')) {
            $this->info("Unable to find 'git' on the system; cannot initialize a git repo.");

            return false;
        }

        $commitMessage = $this->option('message') ?: 'Initial commit.';

        $commands = [
            'git init',
            'git add .',
            'git commit -m "' . str_replace('"', '\"', $commitMessage) . '"',
        ];

        foreach ($commands as $command) {
            $this->runCommand($command);
        }

        return true;
    }

    protected function getDefaultEditor()
    {
        $finder = new ExecutableFinder;

        foreach ($this->editors_gui as $editor) {
            if ($finder->find($editor)) {
                return $editor;
            }
        }

        foreach ($this->editors_terminal as $editor) {
            if ($finder->find($editor)) {
                return $editor;
            }
        }

        return '';
    }

    protected function openTextEditor()
    {
        if ($this->option('editor')) {
            $editor = $this->option('editor');
        } else {
            $editor = $this->getDefaultEditor();
        }

        if (empty($editor)) {
            return $this->warn('Unable to find a text editor.');
        }

        $this->info("Opening {$editor}.");

        $process = new Process("{$editor} .");
        $process->setWorkingDirectory($this->projectpath);

        $process->setTty(true);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    protected function createDatabase($type = 'sqlite')
    {
        if ($type === 'sqlite') {
            $sqlitepath = $this->projectpath . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';

            File::put($sqlitepath, '');

            $this->updateDotEnv([
                'DB_CONNECTION' => 'sqlite',
                'DB_DATABASE' => $sqlitepath,
            ]);

            return true;
        }

        if ($type === 'mysql') {
            $this->warn('The MySQL type is not yet implemented yet.');

            return false;

            /*
            // @todo Offer ways to create users here, one user per db? Auto-generate passwords or always use the same?

            // @todo sanitize this name
            $sql = "CREATE database {$this->projectname}";

            // @todo determine how to do this, either try root/'' or use custom config
            //

            */
        }

        return false;
    }

    protected function openBrowser()
    {
        $this->info('Attempting to find a browser.');

        $browser = $this->option('browser') ?: '';

        if ($this->os->isOSX()) {
            if ($browser === '') {
                $command = 'open "' . $this->projecturl . '"';
            } else {
                $command = 'open -a "' . $browser . '" "' . $this->projecturl . '"';
            }
        }

        if ($this->os->isWindowsLike()) {
            $command = 'start ' . $this->projecturl;
        }

        if ($this->os->isUnixLike() && ! $this->os->isOSX()) {
            $finder = new ExecutableFinder;

            if ($finder->find('xdg-open')) {
                $command = 'xdg-open "' . $this->projecturl . '"';
            } else {
                $this->error("Can't find xdg-open; skipping browser step.");
                return;
            }
        }

        $this->runCommand($command, $this->cwd);
    }

    protected function ensureDotEnvExists($envpath)
    {
        if (! file_exists($envpath)) {
            if (! file_exists($envpath . '.example')) {
                $this->error('No valid .env or .env.example files; skipping .env work.');

                return false;
            }

            copy($envpath . '.example', $envpath);
        }

        return true;
    }

    /**
     * @param $changes array of 'OPTIONNAME' => 'VALUE' pairs
     * @return bool
     */
    public function updateDotEnv(array $changes)
    {
        $envpath = $this->projectpath . DIRECTORY_SEPARATOR . '.env';

        if (! $this->ensureDotEnvExists($envpath)) {
            return false;
        }

        $lines = file($envpath, FILE_IGNORE_NEW_LINES);

        $tracker = [];

        foreach ($lines as $line) {
            // Is it a key=value pair? And not a comment?
            if (false !== strpos($line, '=') && '#' !== substr($line, 0, 1)) {
                list($key) = explode('=', $line, 2);

                // Track keys so we can append key=value pairs from $changes that don't exist currently
                $tracker[$key] = true;

                // Do we have an update for it?
                if (array_key_exists($key, $changes)) {

                    // Change it
                    $out[] = $key . '=' . trim($changes[$key]);

                } else {
                    // Leave it
                    $out[] = $line;
                }

            } else {
                // Empty line or comment; leave it
                $out[] = $line;
            }
        }

        // If new key=value is not in .env then simply append it to the new .env for now
        foreach ($changes as $key => $value) {
            if (! array_key_exists($key, $tracker)) {
                $out[] = $key . '=' . $value;
            }
        }

        return file_put_contents($envpath, implode("\n", $out));
    }

    protected function hasTool($tool)
    {
        if (! array_key_exists($tool, $this->tools)) {
            $this->tools[$tool] = $this->finder->find($tool);
        }

        return $this->tools[$tool];
    }
}
