<?php

namespace App\Commands;

use App\Actions\CreateDatabase;
use App\Actions\CustomizeDotEnv;
use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayLamboWelcome;
use App\Actions\GenerateAppKey;
use App\Actions\InitializeGitRepo;
use App\Actions\InstallNpmDependencies;
use App\Actions\OpenInBrowser;
use App\Actions\OpenInEditor;
use App\Actions\RunLaravelInstaller;
use App\Actions\ValetSecure;
use App\Actions\VerifyDependencies;
use App\Actions\VerifyPathAvailable;
use App\Options;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class NewCommand extends Command
{
    protected $signature;
    protected $description = 'Creates a fresh Laravel application';
    /**
     * @var array
     */
    private $savedConfig;

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
        $this->setConfig();

        app(DisplayLamboWelcome::class)();

        if (! $this->argument('projectName')) {
            app(DisplayHelpScreen::class)();
            exit;
        }

        $this->alert('Creating a Laravel app ' . $this->argument('projectName'));

        try {
            $this->logStep('Verifying Path Availability');
            app(VerifyPathAvailable::class)();

            $this->logStep('Verifying Dependencies');
            app(VerifyDependencies::class)();

            $this->logStep('Running the Laravel Installer');
            app(RunLaravelInstaller::class)();

            $this->logStep('Opening In Editor');
            app(OpenInEditor::class)();

            $this->logStep('Customizing .env and .env.example');
            app(CustomizeDotEnv::class)();

            $this->logStep('Creating database if selected...');
            app(CreateDatabase::class)();

            $this->logStep('Running php artisan key:generate');
            app(GenerateAppKey::class)();

            $this->logStep('Initializing Git Repo');
            app(InitializeGitRepo::class)();

            $this->logStep('Installing NPM dependencies');
            app(InstallNpmDependencies::class)();

            $this->logStep('Running valet secure');
            app(ValetSecure::class)();

            $this->logStep('Opening in Browser');
            app(OpenInBrowser::class)();
        } catch (Exception $e) {
            $this->error("\nFAILURE RUNNING COMMAND:");
            $this->error($e->getMessage());
        }
        // @todo cd into it
    }

    public function setConfig()
    {
        app()->bind('console', function () {
            return $this;
        });

        $configFilePath = sprintf("%s/.lambo/config.json", config('home_dir'));
        $this->savedConfig = File::exists($configFilePath) ? json_decode(File::get($configFilePath), true) : [];

        printf("Saved Config\n%s", print_r($this->savedConfig, true));

        $tld = $this->getTld();

        config()->set('lambo.store', [
            'tld' => $tld,
            'project_name' => $this->argument('projectName'),
            'root_path' => $this->getBasePath(),
            'project_path' => $this->getBasePath() . '/' . $this->argument('projectName'),
            'project_url' => $this->getProtocol() . $this->argument('projectName') . '.' . $tld,
            'database_username' => $this->getDatabaseUsername(),
            'database_password' => $this->getDatabasePassword(),
        ]);

        printf("lambo.store\n%s", print_r(config()->get('lambo.store'), true));
    }

    public function getTld()
    {
        $home = config('home_dir');

        if (File::exists($home . '/.config/valet/config.json')) {
            return json_decode(File::get($home . '/.config/valet/config.json'))->tld;
        }

        return json_decode(File::get($home . '/.valet/config.json'))->domain;
    }

    public function getBasePath()
    {
        if ($this->option('path')) {
            return str_replace('~', config('home_dir'), $this->option('path'));
        }

        if(Arr::has($this->savedConfig, 'path'))
        {
            return str_replace('~', config('home_dir'), Arr::get($this->savedConfig, 'path'));
        }

        return getcwd();
    }

    public function getProtocol()
    {
        // @todo: If securing, change to https
        return 'http://';
    }

    protected function getDatabaseUsername()
    {
        if ($this->option('dbuser')) {
            return $this->option('dbuser');
        }

        if (Arr::exists($this->savedConfig, 'db_username')) {
            return Arr::get($this->savedConfig, 'db_username');
        }

        return 'root';
    }

    protected function getDatabasePassword()
    {
        if ($this->option('dbpassword')) {
            return $this->option('dbpassword');
        }

        if (Arr::exists($this->savedConfig, 'db_password')) {
            return Arr::get($this->savedConfig, 'db_password');
        }

        return '';
    }

    public function logStep($step)
    {
        if ($this->option('verbose')) {
            $this->comment("$step...\n");
        }
    }
}
