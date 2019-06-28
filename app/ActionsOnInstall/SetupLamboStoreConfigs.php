<?php

namespace App\ActionsOnInstall;

use LogicException;
use App\Support\BaseAction;
use Illuminate\Support\Facades\File;

class SetupLamboStoreConfigs extends BaseAction
{
    /**
     * Set the configurations needed in Lambo Store.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $this->setProjectName();

        try {
            $this->setInstallPath();
        } catch (LogicException $exception) {
            $this->console->error($exception->getMessage());
            exit(1);
        }

        $this->setProjectPath();

        $this->setProjectUrl();

        $this->setDbName();

        $this->checkStore();
    }

    /**
     * Set the Project Name.
     *
     */
    protected function setProjectName(): void
    {
        config()->set('lambo.store.project_name', $this->console->argument('projectName'));
    }

    /**
     * Set the installation path.
     *
     * @return void
     */
    protected function setInstallPath(): void
    {
        $configInstallPath = config('lambo.config.path', false);

        if (! $configInstallPath) {
            config()->set('lambo.store.install_path', $this->console->currentWorkingDir);
            return;
        }

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

        if (! File::isDirectory($installPath)) {
            throw new LogicException("Directory {$installPath} doesn't exist.");
        }

        config()->set('lambo.store.install_path', $installPath);
    }

    /**
     * Set the project url.
     *
     * @return void
     */
    protected function setProjectUrl(): void
    {
        $url = 'http://' . config('lambo.store.project_name') . str_start(config('lambo.config.tld'), '.');

        config()->set('lambo.store.project_url', $url);
    }

    /**
     * Set the project path.
     *
     * @return void
     */
    protected function setProjectPath(): void
    {
        $installPath = config('lambo.store.install_path', false);

        $projectPath = str_finish($installPath, '/') . config('lambo.store.project_name');

        config()->set('lambo.store.project_path', $projectPath);
    }

    /**
     * Set the database name.
     *
     * @return void
     */
    protected function setDbName(): void
    {
        $projectName = config('lambo.store.project_name');

        $dbName = str_replace('-', '_', $projectName);

        config()->set('lambo.store.db_name', $dbName);
    }

    /**
     * Perform a check that all needed values in the Lambo Store were successfully set.
     *
     * @return void
     */
    protected function checkStore(): void
    {
        $store = config('lambo.store', [ 'default' => false ]);

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
