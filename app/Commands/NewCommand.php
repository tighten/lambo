<?php

namespace App\Commands;

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
        // @todo open editor
        // @todo npm install
        // @todo update .env.example and re-copy it to .env
        // @todo php artisan key:generate
        // @todo git init, add ., commit -m
        // @todo valet secure
        // @todo open browser
        // @todo cd into it
    }

    public function setConfig()
    {
        app()->bind('console', function () {
            return $this;
        });

        config()->set('lambo.store', [
            'project_name' => $this->argument('projectName'),
        ]);
    }

    public function fancyNotice($message)
    {
        $this->info('***********************************************');
        $this->info($message);
        $this->info('***********************************************');
    }
}
