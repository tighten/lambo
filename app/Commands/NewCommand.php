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
                        {--b|browser : Browser you want to open the project in}
                        {--c|createdb= : Create a database; pass in sqlite or mysql}
                        {--d|dev : Choose the dev branch instead of master}
                        {--e|editor= : Text editor to open the project in}
                        {--l|link : Create a Valet link to the project directory}
                        {--m|message= : Set the first commit message}
                        {--y|node : Set to execute yarn or npm install}
                        {--p|path= : Base path for the installation, otherwise CWD is used}';

    protected $projectname = '';

    protected $projectpath = '';

    protected $projecturl = '';

    protected $cwd = '';

    protected $basepath = '';

    protected $commitmessage = 'Initial commit.';

    protected $editors_terminal = ['vim', 'vi', 'nano', 'pico', 'ed', 'emacs', 'nvim'];

    protected $editors_gui = ['pstorm', 'subl', 'atom', 'textmate', 'geany'];

    protected $editor = '';

    protected $tools = [];

    protected $tld = 'dev';

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
    public function __construct(ExecutableFinder $finder)
    {
        parent::__construct();

        $this->cwd = getcwd();
        $this->basepath = $this->cwd;

        $this->os = new Detector;
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

        if (! $this->hasTool('laravel')) {
            $this->error('Unable to find laravel installer so I must exit. One day I will use composer here instead of exiting.');
            exit;
        }

        if (is_dir($this->projectpath)) {
            if (! $this->askToAndRemoveProject()) {
                $this->error('Goodbye!');
                exit;
            }
        }

        if ($this->option('dev')) {
            $this->warn('The laravel installation will use the latest developmental branch by passing in --dev');
            $branch = ' --dev';
        } else {
            $branch = '';
        }

        $command = "laravel new {$this->projectname}$branch";

        $this->info("Creating a new project named {$this->projectname}");
        $this->info("Executing command '$command' in directory {$this->basepath}");

        $process = new Process($command);
        $process->setWorkingDirectory($this->basepath);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // @todo Determine why the above outputs before this point; so the following getOutput() call does nothing
        $this->line($process->getOutput());

        if ($this->replaceEnvVariables()) {
            $this->info('I replaced .env variables in your new Laravel application');
        }

        if ($this->option('auth')) {
            $this->doAuth();
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
                $this->warn("Your defined '--path $path' is not a directory, so I am skipping it and using '{$this->basepath}' instead.");
            }
        }

        $this->projectpath = $this->basepath . DIRECTORY_SEPARATOR . $this->projectname;
    }

    protected function askToAndRemoveProject()
    {
        $this->info("The directory '{$this->projectpath}' already exists.");

        if ($this->confirm("Shall I proceed by removing the following directory? {$this->projectpath}")) {
            $fs = new Filesystem;

            // @todo Use laravel --force here instead of deleteDirectory()?
            if ($fs->deleteDirectory($this->projectpath)) {
                $this->info("I removed the following directory: {$this->projectpath}");

                return true;
            } else {
                $this->error("I was unable to remove the '{$this->projectpath}' directory so I must exit.");

                return false;
            }
        } else {
            $this->error("You have chosen to not remove the '{$this->projectpath}' directory so I must exit.");

            return false;
        }
    }

    protected function replaceEnvVariables()
    {
        // @todo make this a configuration option
        $changes = [
            'DB_DATABASE' => $this->projectname,
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
            'APP_URL'     => $this->projecturl,
        ];

        return $this->updateDotEnv($changes);
    }

    protected function doNodeOrYarn()
    {
        $command = null;
        if ($this->hasTool('yarn')) {
            $command = 'yarn';
        } elseif ($this->hasTool('npm')) {
            $command = 'npm install';
        }

        if (empty($command)) {
            $this->error('Either yarn or npm are required');

            return false;
        }

        $this->info("Executing $command now; in {$this->projectpath}");

        $process = new Process($command);
        $process->setWorkingDirectory($this->projectpath);
        $process->start();

        $iterator = $process->getIterator($process::ITER_SKIP_ERR | $process::ITER_KEEP_OUTPUT);
        foreach ($iterator as $data) {
            $this->line($data);
        }
    }

    protected function chooseDatabase($dbtype)
    {
        if (! in_array($dbtype, $this->dbtypes)) {
            $this->info("You passed in '--createdb '{$dbtype}' but I do not understand");
            $type = $this->anticipate('What type of database would you like to install? Options are '.implode(' or ', $this->dbtypes) . '.', $this->dbtypes, $this->dbtypes[0]);
        } else {
            $type = $dbtype;
        }

        if (! in_array($type, $this->dbtypes)) {
            $this->alert("Now you're being silly. Entering '$type', really? Okay, I won't create a database for you.");
        } else {
            $this->info("I am creating a new $type database");
            if ($this->createDatabase($type)) {
                $this->info("I am executing 'php artisan migrate:fresh'");
                $this->migrateFresh();
            }
        }

    }

    protected function doAuth()
    {
        $command = 'php artisan make:auth';

        $this->info("Executing $command");

        $process = new Process($command);
        $process->setWorkingDirectory($this->projectpath);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->line($process->getOutput());
    }

    protected function doValetLink()
    {
        if (! $this->hasTool('valet')) {
            $this->warn('Cannot find valet on your system so a valet link was not created.');

            return false;
        }

        $command = "valet link {$this->projectname}";
        $this->info("Linking valet by executing '$command' in {$this->basepath}");

        $process = new Process($command);
        $process->setWorkingDirectory($this->projectpath);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->line($process->getOutput());
        return true;
    }

    protected function doGit()
    {
        if ($this->option('message')) {
            $this->commitmessage = $this->option('message');
        }

        if (! $this->hasTool('git')) {
            $this->info("Unable to find 'git' on the system so I cannot initialize a git repo in '{$this->projectpath}'");

            return false;
        }

        $process = new Process('dummy command');
        $process->setWorkingDirectory($this->projectpath);

        $commands = [
            'git init',
            'git add .',
            'git commit -m "'.str_replace('"', '\"', $this->commitmessage).'"',
        ];

        foreach ($commands as $command) {
            $process->setCommandLine($command);
            $process->run();
            $this->line($process->getOutput());
        }

        return true;
    }

    protected function openTextEditor()
    {
        if ($this->option('editor')) {
            $editor = $this->option('editor');
        } else {
            $finder = new ExecutableFinder;
            foreach ($this->editors_gui as $_editor) {
                if ($finder->find($_editor)) {
                    $editor = $_editor;
                    break;
                }
            }
            if (empty($editor)) {
                foreach ($this->editors_terminal as $_editor) {
                    if ($finder->find($_editor)) {
                        $editor = $_editor;
                        break;
                    }
                }
            }
        }

        if (empty($editor)) {
            $this->warn('Unable to find a text editor to open, skipping this step.');

            return false;
        }

        $this->info("Found editor $editor so am opening it now");

        $process = new Process("{$editor} .");
        $process->setWorkingDirectory($this->projectpath);

        $process->setTty(true);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
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
            // @todo Offer ways to create users here, one user per db? Auto-generate passwords or always use the same?

            // @todo sanitize this name
            $sql = "CREATE database {$this->projectname}";

            // @todo determine how to do this, either try root/'' or use custom config
            //
            $this->warn('The MySQL type is not yet implemented, sorry about that.');
        }

        return false;
    }

    protected function migrateFresh()
    {
        $process = new Process('php artisan migrate:fresh');
        $process->setWorkingDirectory($this->projectpath);
        $process->run();
        $this->line($process->getOutput());
    }

    protected function openBrowser()
    {
        $this->info('Attempting to find a browser to open this in.');

        $browser = '';
        if ($this->option('browser')) {
            $browser = $this->option('browser');
        }

        if ($this->os->isOSX()) {
            if ($browser === '') {
                $command = 'open "' . $this->projecturl . '"';
            } else {
                $command = 'open -a "' . $browser . '" "' . $this->projecturl . '"';
            }
        }

        if ($this->os->isWindowsLike()) {
            $command = '';
        }

        if ($this->os->isUnixLike()) {
            $finder = new ExecutableFinder;
            if ($finder->find('xdg-open')) {
                $command = 'xdg-open "' . $this->projecturl . '"';
            }
        }

        if (isset($command)) {
            $this->info("Opening in your browser now by executing '$command'");
            $process = new Process($command);
            $process->setWorkingDirectory($this->cwd);
            $process->run();
            $this->line($process->getOutput());
        }
    }


    /**
     * @param $changes array of 'OPTIONNAME' => 'VALUE' pairs
     * @return bool
     */
    public function updateDotEnv($changes)
    {
        // We'll always write to this path
        $envpath = $this->projectpath.DIRECTORY_SEPARATOR.'.env';
        if (file_exists($envpath)) {
            $lines = file($this->projectpath.DIRECTORY_SEPARATOR . '.env', FILE_IGNORE_NEW_LINES);
        } else {
            if (file_exists($this->projectpath.DIRECTORY_SEPARATOR . '.env.example')) {
                $lines = file($this->projectpath.DIRECTORY_SEPARATOR . '.env.example', FILE_IGNORE_NEW_LINES);
            } else {
                $this->error('I could not find a valid .env or .env.example file to update; this is not good.');
                return false;
            }
        }
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
