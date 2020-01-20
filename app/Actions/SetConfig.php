<?php

namespace App\Actions;

use Dotenv\Dotenv;
use Facades\App\LamboConfig;
use Facades\App\Utilities;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class SetConfig
{
    const PROJECTPATH = 'PROJECTPATH';
    const MESSAGE = 'MESSAGE';
    const QUIET = 'QUIET';
    const QUIET_SHELL = 'QUIET_SHELL';
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

    public $keys = [
        self::PROJECTPATH,
        self::MESSAGE,
        self::QUIET,
        self::QUIET_SHELL,
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
            'create_database' => $this->getBooleanOptionValue('create-db', self::CREATE_DATABASE),
            'commit_message' => $this->getOptionValue('message', self::MESSAGE) ?? 'Initial commit.',
            'valet_link' => $this->getBooleanOptionValue('link', self::LINK),
            'valet_secure' => $this->getBooleanOptionValue('secure', self::SECURE),
            'quiet' => $this->getBooleanOptionValue('quiet', self::QUIET),
            'quiet-shell' => $this->getBooleanOptionValue('quiet-shell', self::QUIET_SHELL),
            'editor' => $this->getOptionValue('editor', self::CODEEDITOR),
            'node' => $this->getBooleanOptionValue('node', self::NODE),
            'mix' => $this->getBooleanOptionValue('mix', self::MIX),
            'dev' => $this->getBooleanOptionValue('dev', self::DEVELOP),
            'auth' => $this->getBooleanOptionValue('auth', self::AUTH),
            'browser' => $this->getOptionValue('browser', self::BROWSER),
            'frontend' => $this->getFrontendType(),
        ]);

        dump(config('lambo.store'));
    }

    public function loadSavedConfig()
    {
        (Dotenv::create(LamboConfig::configDir(), 'config'))->safeLoad();

        $loaded = collect($this->keys)->reject(function ($key) {
            return ! Arr::has($_ENV, $key);
        })->mapWithKeys(function($value){
            return [$value => $_ENV[$value]];
        })->toArray();

        return $loaded;
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

    /**
     * Cast "1", "true", "on" and "yes" to boolean true. Everything else to boolean false.
     */
    private function getBooleanOptionValue($optionCommandLineName, $optionConfigFileName = null)
    {
        return filter_var($this->getOptionValue($optionCommandLineName, $optionConfigFileName), FILTER_VALIDATE_BOOLEAN);
    }

    protected function getFrontendType()
    {
        $frontEndType = $this->getOptionValue('frontend', self::FRONTEND);

        if (empty($frontEndType) || is_null($frontEndType)) {
            return false;
        }

        if (in_array($frontEndType, ConfigureFrontendFramework::FRAMEWORKS)) {
            return $frontEndType;
        }
        app('console')->error("Oops. '{$frontEndType}' is not a valid option for -f, --frontend.\nValid options are: bootstrap, react or vue.");
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
        return $this->getBooleanOptionValue('secure', self::SECURE) ? 'https://' : 'http://';
    }

    protected function getDatabaseName()
    {
        return $this->getOptionValue('dbname', self::DB_NAME)
            ? Utilities::prepNameForDatabase($this->getOptionValue('dbname', self::DB_NAME))
            : Utilities::prepNameForDatabase($this->argument('projectName'));
    }

    public function argument($key)
    {
        return app('console')->argument($key);
    }

    public function option($key)
    {
        return app('console')->option($key);
    }
}
