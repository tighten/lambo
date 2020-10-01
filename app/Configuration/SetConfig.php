<?php

namespace App\Configuration;

use App\LamboException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetConfig
{
    private $commandLineConfiguration;
    private $savedConfiguration;
    private $shellConfiguration;

    protected $fullFlags = [
        LamboConfiguration::CREATE_DATABASE,
        LamboConfiguration::MIGRATE_DATABASE,
        LamboConfiguration::VALET_LINK,
        LamboConfiguration::VALET_SECURE,
    ];

    public function __construct(CommandLineConfiguration $commandLineConfiguration, SavedConfiguration $savedConfiguration, ShellConfiguration $shellConfiguration)
    {
        $this->commandLineConfiguration = $commandLineConfiguration;
        $this->savedConfiguration = $savedConfiguration;
        $this->shellConfiguration = $shellConfiguration;
    }

    public function __invoke($defaultConfiguration)
    {
        foreach ($defaultConfiguration as $configurationKey => $default) {

            $methodName = 'get' . Str::of($configurationKey)->studly();
            if (method_exists($this, $methodName)) {
                config(["lambo.store.{$configurationKey}" => $this->$methodName($configurationKey, $default)]);
                continue;
            }
            config(["lambo.store.{$configurationKey}" => $this->get($configurationKey, $default)]);
        }
        // These are set here because they require that the, command line
        // arguments/options, saved configuration and shell environment
        // configurations have been merged prior to setting.
        // @todo: vvv should we check that the required config variables are set? vvv
        config(["lambo.store.project_path" => config('lambo.store.root_path') . "/" . config('lambo.store.project_name')]);
        config(["lambo.store.project_url" => $this->getProjectURL()]);

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
        $protocol = config("lambo.store.valet_secure") ? 's' : '';
        return sprintf("http%s://%s.%s", $protocol, config('lambo.store.project_name'), config('lambo.store.tld'));
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
        if(app('console-writer')->getVerbosity() > SymfonyStyle::VERBOSITY_NORMAL) {
            return true;
        }

        return $this->get($key, $default);
    }
}
