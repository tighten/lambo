<?php

namespace App\Actions;

use App\InteractsWithLamboConfig;
use Dotenv\Dotenv;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetConfig
{
    use LamboAction, InteractsWithLamboConfig;

    const PROJECTPATH = 'PROJECTPATH';
    const MESSAGE = 'MESSAGE';
    const QUIET = 'QUIET';
    const DEVELOP = 'DEVELOP';
    const AUTH = 'AUTH';
    const FRONTEND = 'FRONTEND';
    const NODE = 'NODE';
    const MIX = 'MIX';
    const CODEEDITOR = 'CODEEDITOR';
    const BROWSER = 'BROWSER';
    const LINK = 'LINK';
    const SECURE = 'SECURE';
    const DB_NAME = 'DB_NAME';
    const DB_USERNAME = 'DB_USERNAME';
    const DB_PASSWORD = 'DB_PASSWORD';
    const CREATE_DATABASE = 'CREATE_DATABASE';
    const FULL = 'FULL';
    const WITH_OUTPUT = 'WITH_OUTPUT';

    public $keys = [
        self::PROJECTPATH,
        self::MESSAGE,
        self::QUIET,
        self::DEVELOP,
        self::AUTH,
        self::FRONTEND,
        self::NODE,
        self::MIX,
        self::CODEEDITOR,
        self::BROWSER,
        self::LINK,
        self::SECURE,
        self::CREATE_DATABASE,
        self::DB_NAME,
        self::DB_USERNAME,
        self::DB_PASSWORD,
        self::FULL,
        self::WITH_OUTPUT,
    ];

    const FRONTEND_FRAMEWORKS = [
        'vue',
        'react',
        'bootstrap',
    ];

    protected $savedConfig;

    public function __construct()
    {
        $this->savedConfig = $this->loadSavedConfig();
    }

    public function __invoke()
    {
        $tld = $this->getTld();

        config()->set('lambo.store', [
            'tld' => $tld,
            'project_name' => $this->argument('projectName'),
            'root_path' => $this->getBasePath(),
            'project_path' => $this->getBasePath() . '/' . $this->argument('projectName'),
            'project_url' => $this->getProtocol() . $this->argument('projectName') . '.' . $tld,
            'database_name' => $this->getDatabaseName(),
            'database_username' => $this->getOptionValue('dbuser', self::DB_USERNAME) ?? 'root',
            'database_password' => $this->getOptionValue('dbpassword', self::DB_PASSWORD) ?? '',
            'create_database' => $this->shouldCreateDatabase(),
            'commit_message' => $this->getOptionValue('message', self::MESSAGE) ?? 'Initial commit.',
            'valet_link' => $this->shouldLink(),
            'valet_secure' => $this->shouldSecure(),
            'quiet' => $this->getBooleanOptionValue('quiet', self::QUIET),
            'with_output' => $this->getBooleanOptionValue('with-output', self::WITH_OUTPUT),
            'editor' => $this->getOptionValue('editor', self::CODEEDITOR),
            'node' => $this->shouldInstallNpmDependencies(),
            'mix' => $this->shouldRunMix(),
            'dev' => $this->getBooleanOptionValue('dev', self::DEVELOP),
            'auth' => $this->shouldInstallAuthentication(),
            'browser' => $this->getOptionValue('browser', self::BROWSER),
            'frontend' => $this->getFrontendType(),
            'full' => $this->getBooleanOptionValue('full'),
        ]);
    }

    public function loadSavedConfig()
    {
        (Dotenv::create($this->configDir(), 'config'))->safeLoad();

        return collect($this->keys)->reject(function ($key) {
            return ! Arr::has($_ENV, $key);
        })->mapWithKeys(function($value){
            return [$value => $_ENV[$value]];
        })->toArray();
    }

    public function getTld()
    {
        $home = config('home_dir');

        if (File::exists($home . '/.config/valet/config.json')) {
            return json_decode(File::get($home . '/.config/valet/config.json'))->tld;
        }

        return json_decode(File::get($home . '/.valet/config.json'))->domain;
    }

    public function getOptionValue($optionCommandLineName, $optionConfigFileName = null)
    {
        if (is_null($optionConfigFileName)) {
            $optionConfigFileName = $optionCommandLineName;
        }

        if ($this->option($optionCommandLineName)) {
            return $this->option($optionCommandLineName);
        }

        if (Arr::has($this->savedConfig, $optionConfigFileName)) {
            return Arr::get($this->savedConfig, $optionConfigFileName);
        }
    }

    /*
     * Cast "1", "true", "on" and "yes" to bool true. Everything else to bool false.
     */
    public function getBooleanOptionValue($optionCommandLineName, $optionConfigFileName = null)
    {
        return filter_var($this->getOptionValue($optionCommandLineName, $optionConfigFileName), FILTER_VALIDATE_BOOLEAN);
    }

    public function getFrontendType()
    {
        $frontEndType = $this->getOptionValue('frontend', self::FRONTEND);

        if (empty($frontEndType) || is_null($frontEndType)) {
            return false;
        }

        if (in_array($frontEndType, self::FRONTEND_FRAMEWORKS)) {
            return $frontEndType;
        }
        $this->error("Oops. '{$frontEndType}' is not a valid option for -f, --frontend.\nValid options are: bootstrap, react or vue.");
        app(DisplayHelpScreen::class)();
        exit();
    }

    public function getBasePath()
    {
        if ($value = $this->getOptionValue('path', self::PROJECTPATH)) {
            return str_replace('~', config('home_dir'), $value);
        }

        return getcwd();
    }

    public function getProtocol()
    {
        return $this->shouldSecure() ? 'https://' : 'http://';
    }

    public function getDatabaseName()
    {
        $configuredDatabaseName = $this->getOptionValue('dbname', self::DB_NAME)
            ? $this->getOptionValue('dbname', self::DB_NAME)
            : $this->argument('projectName');

        if (! Str::contains($configuredDatabaseName, '-')) {
            return $configuredDatabaseName;
        }

        $newDatabaseName = str_replace('-', '_', $configuredDatabaseName);
        $this->warn("Your configured database name <error> {$configuredDatabaseName} </error> contains hyphens which can cause problems in some instances.");
        $this->warn('The hyphens have been replaced with underscores to prevent problems.');
        $this->warn("New database name: <info>{$newDatabaseName}</info>.");
        return $newDatabaseName;
    }

    public function argument($key)
    {
        return app('console')->argument($key);
    }

    public function option($key)
    {
        return app('console')->option($key);
    }

    public function shouldRunMix(): bool
    {
        return $this->getBooleanOptionValue('full')
            || $this->getBooleanOptionValue('mix', self::MIX);
    }

    public function shouldInstallNpmDependencies(): bool
    {
        return $this->shouldRunMix()
            || $this->getBooleanOptionValue('node', self::NODE);
    }

    public function shouldCreateDatabase(): bool
    {
        return $this->getBooleanOptionValue('full')
            || $this->getBooleanOptionValue('create-db', self::CREATE_DATABASE);
    }

    public function shouldLink(): bool
    {
        return $this->getBooleanOptionValue('full')
            || $this->getBooleanOptionValue('link', self::LINK);
    }

    public function shouldSecure(): bool
    {
        return $this->getBooleanOptionValue('full')
            || $this->getBooleanOptionValue('secure', self::SECURE);
    }

    public function shouldInstallAuthentication(): bool
    {
        return $this->getBooleanOptionValue('full')
            || $this->getBooleanOptionValue('auth', self::AUTH);
    }
}
