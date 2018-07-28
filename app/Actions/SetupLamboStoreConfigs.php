<?php

namespace App\Actions;

use LogicException;
use App\Support\BaseAction;
use Illuminate\Support\Facades\File;

class SetupLamboStoreConfigs extends BaseAction
{
    public function __invoke()
    {
        $this->projectName();

        try {
            $this->installPath();
        } catch (LogicException $exception) {
            $this->console->error($exception->getMessage());
            exit(1);
        }

        $this->projectPath();

        $this->projectUrl();

        $this->dbName();

        $this->checkStore();

        if ($manualTest = false) {
            dd(config('lambo-store'));
        }
    }

    /**
     * Stores the Project Name.
     *
     */
    protected function projectName(): void
    {
        config()->set('lambo-store.project_name', $this->console->argument('projectName'));
    }

    /**
     * Stores the installation path.
     *
     */
    protected function installPath(): void
    {
        $configInstallPath = config('lambo.path', false);

        if (collect([false, null])->contains($configInstallPath)) {
            config()->set('lambo-store.install_path', $this->console->currentWorkingDir);
        } else {
            if (starts_with($configInstallPath, '~')) {
                // Path starts with '~', so it's relative to the HOME folder
                $installPath = str_replace('~', $_SERVER['HOME'], $configInstallPath);
            } elseif (starts_with($configInstallPath, '/')) {
                // Path starts with '~', so it's an absolute path
                $installPath = $configInstallPath;
            } else {
                // Path is relative to the working dir
                $installPath = str_finish($this->console->currentWorkingDir, '/') . $configInstallPath;
            }

            if (!File::isDirectory($installPath)) {
                throw new LogicException("Directory {$installPath} doesn't exist.");
            }

            config()->set('lambo-store.install_path', $installPath);
        }
    }

    /**
     * Stores the project url.
     *
     */
    protected function projectUrl(): void
    {
        $url = 'http://' . config('lambo-store.project_name') . str_start(config('lambo.tld'), '.');

        config()->set('lambo-store.project_url', $url);
    }

    /**
     * Stores the project path.
     *
     */
    protected function projectPath(): void
    {
        $installPath = config('lambo-store.install_path', false);

        $projectPath = str_finish($installPath, '/') . config('lambo-store.project_name');

        config()->set('lambo-store.project_path', $projectPath);
    }

    /**
     * Sets the database name
     *
     */
    protected function dbName(): void
    {
        $projectName = config('lambo-store.project_name');

        if (!$projectName) {
            return;
        }

        $dbName = str_replace('-', '_', $projectName);

        config()->set('lambo-store.db_name', $dbName);
    }

    /**
     * Performs a check that all needed values in the Lambo Store where successfully set.
     *
     */
    protected function checkStore(): void
    {
        $store = config('lambo-store', [ 'default' => false ]);

        $exit = false;

        foreach ($store as $key => $value) {
            if (!$value) {
                $this->console->error("Error, could not set value for {$key}");
                $exit = true;
            }
        }

        if ($exit) {
            exit(1);
        }
    }
}
