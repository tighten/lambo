<?php

namespace App\Commands;

use App\Actions\SavedConfig;
use App\Configuration\CommandLineConfiguration;
use App\Configuration\LamboConfiguration;
use App\Configuration\SavedConfiguration;
use App\Configuration\SetConfig;
use App\Configuration\ShellConfiguration;
use App\LamboException;

class EditAfter extends LamboCommand
{
    protected $signature = 'edit-after {--editor= : Open the config file in the specified <info>EDITOR</info> or the system default if none is specified.}';

    protected $description = 'Edit Config File. A new config file is created if one does not already exist.';

    public function handle()
    {
        app()->bind('console', function () {
            return $this;
        });

        $commandLineConfiguration = new CommandLineConfiguration([
            'editor' => LamboConfiguration::EDITOR
        ]);

        $savedConfiguration = new SavedConfiguration([
            'CODEEDITOR' => LamboConfiguration::EDITOR
        ]);

        $shellConfiguration = new ShellConfiguration([
            'EDITOR' => LamboConfiguration::EDITOR
        ]);

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration
        ))([
            LamboConfiguration::EDITOR => 'nano'
        ]);

        try {
            app(SavedConfig::class)->createOrEditConfigFile("after");
        } catch (LamboException $e) {
            app('console-writer')->exception($e->getMessage());
        }
    }
}
