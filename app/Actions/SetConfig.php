<?php

namespace App\Actions;

use Dotenv\Dotenv;
use Facades\App\LamboConfig;
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
    const NODE = 'NODE';
    const CODEEDITOR = 'CODEEDITOR';
    const BROWSER = 'BROWSER';
    const LINK = 'LINK';
    const SECURE = 'SECURE';
    const DB_USERNAME = 'DB_USERNAME';
    const DB_PASSWORD = 'DB_PASSWORD';
    const FRONTEND = 'FRONTEND';

    public $keys = [
        self::PROJECTPATH,
        self::MESSAGE,
        self::QUIET,
        self::QUIET_SHELL,
        self::DEVELOP,
        self::AUTH,
        self::NODE,
        self::CODEEDITOR,
        self::BROWSER,
        self::LINK,
        self::SECURE,
        self::DB_USERNAME,
        self::DB_PASSWORD,
        self::FRONTEND,
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
            'database_username' => $this->getOptionValue('dbuser', self::DB_USERNAME) ?? 'root',
            'database_password' => $this->getOptionValue('dbpassword', self::DB_PASSWORD) ?? '',
            'commit_message' => $this->getOptionValue('message', self::MESSAGE) ?? 'Initial commit.',
            'valet_link' => $this->getBooleanOptionValue('link', self::LINK),
            'valet_secure' => $this->getBooleanOptionValue('secure', self::SECURE),
            'quiet' => $this->getBooleanOptionValue('quiet', self::QUIET),
            'quiet-shell' => $this->getBooleanOptionValue('quiet-shell', self::QUIET_SHELL),
            'editor' => $this->getOptionValue('editor', self::CODEEDITOR),
            'node' => $this->getBooleanOptionValue('node', self::NODE),
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
     * @return mixed
     */
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

    /**
     * Cast "1", "true", "on" and "yes" to boolean true. Everything else to boolean false.
     */
    private function getBooleanOptionValue($optionCommandLineName, $optionConfigFileName = null)
    {
        return filter_var($this->getOptionValue($optionCommandLineName, $optionConfigFileName), FILTER_VALIDATE_BOOLEAN);
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

    public function argument($key)
    {
        return app('console')->argument($key);
    }

    public function option($key)
    {
        return app('console')->option($key);
    }
}
