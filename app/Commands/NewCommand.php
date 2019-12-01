<?php

namespace App\Commands;

use App\Actions\OpenEditor;
use App\Actions\CustomizeDotEnv;
use App\Actions\GenerateAppKey;
use App\Actions\InitializeGitRepo;
use App\Actions\InstallNpmDependencies;
use App\Actions\RunLaravelInstaller;
use App\Actions\VerifyDependencies;
use LaravelZero\Framework\Commands\Command;

class NewCommand extends Command
{
    protected $signature = 'new
        {projectName? : Name of the Laravel project}
    ';

    protected $description = 'Creates a fresh Laravel application';

    public function handle()
    {
        if (! $this->argument('projectName')) {
            dd('@todo show help here;');
        }

        $this->setConfig();

        $this->fancyNotice('Creating a Laravel app ' . $this->argument('projectName'));

        app(VerifyDependencies::class)();
        app(RunLaravelInstaller::class)();
        app(OpenEditor::class)();
        app(CustomizeDotEnv::class)();
        app(GenerateAppKey::class)();
        app(InitializeGitRepo::class)();
        app(InstallNpmDependencies::class)();
        // @todo valet secure
        // @todo open browser
        // @todo cd into it
    }

    public function setConfig()
    {
        app()->bind('console', function () {
            return $this;
        });

        $tld = 'test';
// @todo get tld

        //if [[ -f ~/.config/valet/config.json ]]; then
//     TLD=$(php -r "echo json_decode(file_get_contents('$HOME/.config/valet/config.json'))->tld;")
// else
//     TLD=$(php -r "echo json_decode(file_get_contents('$HOME/.valet/config.json'))->domain;")
// fi

        config()->set('lambo.store', [
            'install_path' => getcwd(),
            'tld' => $tld,
            'project_name' => $this->argument('projectName'),
            'project_path' => getcwd() . '/' . $this->argument('projectName'),
            'project_url' => $this->argument('projectName') . '.' . $tld,
        ]);
    }

    public function fancyNotice($message)
    {
        $this->info('***********************************************');
        $this->info($message);
        $this->info('***********************************************');
    }
}
