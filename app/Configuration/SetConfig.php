<?php

namespace App\Configuration;

use App\Actions\InstallBreeze;
use App\Actions\InstallJetstream;
use App\Commands\Debug;
use App\Commands\NewCommand;
use App\ConsoleWriter;
use App\LamboException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetConfig
{
    use Debug;

    protected $consoleWriter;
    protected $fullFlags = [
        LamboConfiguration::CREATE_DATABASE,
        LamboConfiguration::MIGRATE_DATABASE,
        LamboConfiguration::VALET_LINK,
        LamboConfiguration::VALET_SECURE,
    ];
    protected $options;

    private $commandLineConfiguration;
    private $savedConfiguration;
    private $shellConfiguration;
    private $commandLineInput;

    public function __construct(
        CommandLineConfiguration $commandLineConfiguration,
        SavedConfiguration $savedConfiguration,
        ShellConfiguration $shellConfiguration,
        ConsoleWriter $consoleWriter,
        InputInterface $commandLineOptions
    ) {
        $this->commandLineConfiguration = $commandLineConfiguration;
        $this->savedConfiguration = $savedConfiguration;
        $this->shellConfiguration = $shellConfiguration;
        $this->consoleWriter = $consoleWriter;

        $this->commandLineInput = array_filter($commandLineOptions->getOptions(), function ($value, $key) use ($commandLineOptions) {
            return $commandLineOptions->hasParameterOption("--{$key}");
        }, ARRAY_FILTER_USE_BOTH);
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
            $projectPath = config('lambo.store.root_path') . '/' . config('lambo.store.project_name');
            config(['lambo.store.project_path' => $projectPath]);
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
        if (isset($this->commandLineConfiguration->$configurationKey)) {
            return $this->commandLineConfiguration->$configurationKey;
        }

        if (isset($this->savedConfiguration->$configurationKey)) {
            return $this->savedConfiguration->$configurationKey;
        }

        if (isset($this->shellConfiguration->$configurationKey)) {
            return $this->shellConfiguration->$configurationKey;
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

        throw new LamboException(
            implode(PHP_EOL, [
                'Unable to find valet domain (tld) configuration.',
                'No Valet configuration located at either of the following locations:',
                "- {$valetConfig}",
                "- {$legacyValetConfig}",
            ])
        );
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
        return sprintf(
            'http%s://%s.%s',
            config('lambo.store.valet_secure') ? 's' : '',
            config('lambo.store.project_name'),
            config('lambo.store.tld')
        );
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

    private function getBreeze(string $key, $default)
    {
        $this->ensureOnlyOneStarterKitSelected();

        if (! Arr::has($this->commandLineInput, 'breeze')) {
            return false;
        }

        config(['lambo.store.jetstream' => false]);

        return in_array($this->commandLineInput['breeze'], InstallBreeze::VALID_STACKS)
            ? $this->commandLineInput['breeze']
            : $this->configureBreezeStack();
    }

    private function getJetstream(string $key, $default)
    {
        $this->ensureOnlyOneStarterKitSelected();

        if (! Arr::has($this->commandLineInput, 'jetstream')) {
            return false;
        }

        config(['lambo.store.breeze' => false]);

        return in_array($this->commandLineInput['jetstream'], InstallJetstream::VALID_CONFIGURATIONS)
            ? $this->commandLineInput['jetstream']
            : $this->configureJetstreamStack();
    }

    private function configureBreezeStack(): string
    {
        $this->consoleWriter->note("Laravel Breeze does not provide a <fg=yellow>'{$this->commandLineInput['breeze']}'</> front-end.");
        $choice = $this->consoleWriter->choice('Please choose one of the following', array_keys(InstallBreeze::VALID_STACKS));
        $this->consoleWriter->ok("Using Laravel Breeze with a {$choice} front-end.");

        return Str::lower($choice);
    }

    private function configureJetstreamStack(): string
    {
        $this->consoleWriter->note("<fg=yellow>'{$this->commandLineInput['jetstream']}'</> is not a valid Laravel Jetstream configuration.");
        $stack = $this->consoleWriter->choice('Please choose a front-end', array_keys(InstallJetstream::VALID_STACKS));
        $teams = $this->consoleWriter->confirm('Would you like to use teams?');
        $this->consoleWriter->ok(sprintf('Using %s%s.', $stack, $teams ? ' and teams' : ' without teams'));

        return InstallJetstream::VALID_STACKS[$stack] . ($teams ? ',teams' : '');
    }

    private function ensureOnlyOneStarterKitSelected(): void
    {
        if (Arr::has($this->commandLineInput, ['breeze', 'jetstream'])) {
            $this->consoleWriter->newLine();
            $this->consoleWriter->note('Only one starter-kit may be configured.');

            $choice = $this->consoleWriter->choice('Please choose a starter-kit:', [
                'None',
                'Laravel Breeze',
                'Laravel Jetstream',
            ], 0);

            switch ($choice) {
                case 'Laravel Breeze':
                    unset($this->commandLineConfiguration->jetstream);
                    Arr::forget($this->commandLineInput, 'jetstream');
                    $this->consoleWriter->ok('Using Laravel Breeze');
                    break;
                case 'Laravel Jetstream':
                    Arr::forget($this->commandLineInput, 'breeze');
                    unset($this->commandLineConfiguration->breeze);
                    $this->consoleWriter->ok('Using Laravel Jetstream');
                    break;
                case 'None':
                    Arr::forget($this->commandLineInput, 'jetstream');
                    unset($this->commandLineConfiguration->jetstream);
                    Arr::forget($this->commandLineInput, 'breeze');
                    unset($this->commandLineConfiguration->breeze);
                    $this->consoleWriter->ok('Skipping starter-kit installation.');
                    break;
            }
        }
    }
}
