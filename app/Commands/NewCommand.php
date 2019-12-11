<?php

namespace App\Commands;

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
use App\Options;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class NewCommand extends Command
{
    protected $signature;
    protected $description = 'Creates a fresh Laravel application';

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

        app(VerifyDependencies::class)();
        app(RunLaravelInstaller::class)();
        app(OpenInEditor::class)();
        app(CustomizeDotEnv::class)();
        app(GenerateAppKey::class)();
        app(InitializeGitRepo::class)();
        app(InstallNpmDependencies::class)();
        app(ValetSecure::class)();
        app(OpenInBrowser::class)();
        // @todo cd into it
    }

    public function setConfig()
    {
        app()->bind('console', function () {
            return $this;
        });

        $tld = $this->getTld();

        config()->set('lambo.store', [
            'install_path' => getcwd(),
            'tld' => $tld,
            'project_name' => $this->argument('projectName'),
            'project_path' => getcwd() . '/' . $this->argument('projectName'),
            'project_url' => $this->argument('projectName') . '.' . $tld,
        ]);
    }

    public function getTld()
    {
        $home = config('home_dir');

        if (File::exists($home . '/.config/valet/config.json')) {
            return json_decode(File::get($home . '/.config/valet/config.json'))->tld;
        }

        return json_decode(File::get($home . '/.valet/config.json'))->domain;
    }
}
