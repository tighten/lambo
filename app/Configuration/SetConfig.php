<?php

namespace App\Configuration;

use App\LamboException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetConfig
{
    private $commandLineConfiguration;
    private $savedConfiguration;
    private $shellConfiguration;

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
                Config::set("lambo.store.{$configurationKey}", $this->$methodName($configurationKey, $default));
                continue;
            }
            Config::set("lambo.store.{$configurationKey}", $this->get($configurationKey, $default));
        }
        // These are set here because they require that the, command line
        // arguments/options, saved configuration and shell environment
        // configurations have been merged prior to setting.
        // @todo: vvv should we check that the required config variables are set? vvv
        Config::set("lambo.store.project_path", Config::get('lambo.store.root_path') . "/" . Config::get('lambo.store.project_name'));
        Config::set("lambo.store.project_url", $this->getProjectURL());
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
        $valetConfig = Config::get('home_dir') . '/.config/valet/config.json';
        $legacyValetConfig = Config::get('home_dir') . '/.valet/config.json';

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
        $protocol = Config::get("lambo.store.valet_secure") ? 's' : '';
        return sprintf("http%s://%s.%s", $protocol, Config::get('lambo.store.project_name'), Config::get('lambo.store.tld'));
    }

    private function getAuth(string $key, $default)
    {
        return $this->fullOrConfigured($key, $default);
    }

    private function getValetSecure(string $key, $default)
    {
        return $this->fullOrConfigured($key, $default);
    }

    private function getCreateDatabase(string $key, $default)
    {
        return $this->fullOrConfigured($key, $default);
    }

    public function getNode(string $key, $default)
    {
        if ($this->get('mix', false)) {
            return true;
        }

        return $this->fullOrConfigured($key, $default);
    }

    public function getMix(string $key, $default)
    {
        return $this->fullOrConfigured($key, $default);
    }

    public function getValetLink(string $key, $default)
    {
        return $this->fullOrConfigured($key, $default);
    }

    private function fullOrConfigured(string $key, $default): bool
    {
        if ($this->commandLineConfiguration->full) {
            return true;
        }

        return $this->get($key, $default);
    }
}
