<?php

namespace App\Configuration;

use App\Commands\NewCommand;
use App\ConsoleWriter;
use App\LamboException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetConfig
{
    private $commandLineConfiguration;
    private $savedConfiguration;
    private $shellConfiguration;
    protected $consoleWriter;

    protected $fullFlags = [
        LamboConfiguration::CREATE_DATABASE,
        LamboConfiguration::MIGRATE_DATABASE,
        LamboConfiguration::VALET_LINK,
        LamboConfiguration::VALET_SECURE,
    ];

    public function __construct(CommandLineConfiguration $commandLineConfiguration, SavedConfiguration $savedConfiguration, ShellConfiguration $shellConfiguration, ConsoleWriter $consoleWriter)
    {
        $this->commandLineConfiguration = $commandLineConfiguration;
        $this->savedConfiguration = $savedConfiguration;
        $this->shellConfiguration = $shellConfiguration;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke($defaultConfiguration)
    {
        foreach ($defaultConfiguration as $configurationKey => $default) {
            $methodName = 'get' . Str::of($configurationKey)->studly();
            if (method_exists($this, $methodName)) {
                config(["lambo.store.{$configurationKey}" => $this->$methodName($configurationKey, $default)]);
            } else {
                config(["lambo.store.{$configurationKey}" => $this->get($configurationKey, $default)]);
            }
        }

        // If we're in the "new" command, generate a few config items which
        // require others to be set above first.
        if (config('lambo.store.command') === NewCommand::class) {
            config(['lambo.store.project_path' => config('lambo.store.root_path') . '/' . config('lambo.store.project_name')]);
            config(['lambo.store.project_url' => $this->getProjectURL()]);
        }

        if (config('lambo.store.full')) {
            foreach ($this->fullFlags as $fullFlag) {
                config(["lambo.store.{$fullFlag}" => true]);
            }
        }
    }

    private function get(string $configurationKey, $default)
    {
        if ($configuration = $this->commandLineConfiguration->$configurationKey) {
            return $configuration;
        }

        if ($configuration = $this->savedConfiguration->$configurationKey) {
            return $configuration;
        }

        if ($configuration = $this->shellConfiguration->$configurationKey) {
            return $configuration;
        }

        return $default;
    }

    private function getTld(): string
    {
        $valetConfig = config('home_dir') . '/.config/valet/config.json';
        $legacyValetConfig = config('home_dir') . '/.valet/config.json';

        if (File::isFile($valetConfig)) {
            return json_decode(File::get($valetConfig))->tld;
        }

        if (File::isFile($legacyValetConfig)) {
            return json_decode(File::get($legacyValetConfig))->domain;
        }

        throw new LamboException("Unable to find valet domain (tld) configuration.\nNo Valet configuration located at either of the following locations: \n  - {$valetConfig}\n  - {$legacyValetConfig}");
    }

    private function getRootPath(string $key, $default)
    {
        $configuredKeyValue = $this->get($key, $default);

        return ($configuredKeyValue === $default)
            ? $default
            : str_replace('~', config('home_dir'), $configuredKeyValue);
    }

    private function getDatabaseName(string $key, $default)
    {
        return str_replace('-', '_', $this->get($key, $default));
    }

    private function getProjectURL(): string
    {
        return sprintf("http%s://%s.%s",
            config('lambo.store.valet_secure') ? 's' : '',
            config('lambo.store.project_name'),
            config('lambo.store.tld'));
    }

    private function getMigrateDatabase(string $key, $default)
    {
        if ($this->commandLineConfiguration->inertia || $this->commandLineConfiguration->livewire) {
            return true;
        }

        return $this->get($key, $default);
    }

    private function getWithOutput(string $key, $default): bool
    {
        if ($this->consoleWriter->getVerbosity() > SymfonyStyle::VERBOSITY_NORMAL) {
            return true;
        }

        return $this->get($key, $default);
    }
}
