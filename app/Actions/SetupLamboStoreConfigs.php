<?php

namespace App\Actions;

use App\Support\BaseAction;

class SetupLamboStoreConfigs extends BaseAction
{
    public function __invoke()
    {
        $this->console->projectName = $this->console->argument('projectName');

        config()->set('lambo-store.project_name', $this->console->argument('projectName'));

        $this->installPath();

        $this->checkStore();
    }

    protected function installPath()
    {
        $configInstallPath = config('lambo.path', false);

        if (collect([false, null])->contains($configInstallPath)) {
            config()->set('lambo-store.install_path', $this->console->currentWorkingDir);
        } else {
            config()->set('lambo-store.install_path', $configInstallPath);
        }
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
