<?php

namespace App;

use Illuminate\Support\Facades\File;

trait InteractsWithLamboConfig
{
    public function configDir()
    {
        return config('home_dir') . '/.lambo';
    }

    public function createOrEditConfigFile(string $fileName, string $fileTemplate)
    {
        $this->ensureConfigFileExists($fileName, $fileTemplate);

        $this->editConfigFile($this->getConfigFilePath($fileName));
    }

    protected function ensureConfigFileExists(string $fileName, string $fileTemplate)
    {
        $this->ensureConfigDirExists();

        if (! $this->configFileExists($fileName)) {
            app('console')->info("File: {$this->getConfigFilePath($fileName)} does not exist, creating it now.");
            File::put($this->getConfigFilePath($fileName), $fileTemplate);
        }
    }

    public function ensureConfigDirExists()
    {
        if (! File::exists($this->configDir())) {
            app('console')->info("Config directory: {$this->configDir()} does not exist, creating it now.");
            File::makeDirectory($this->configDir());
        }
    }

    public function configFileExists($fileName)
    {
        return File::exists($this->configDir() . '/' . $fileName);
    }

    public function getConfigFilePath(string $fileName)
    {
        return  $this->configDir() . "/" . $fileName;
    }

    protected function editConfigFile(string $filePath)
    {
        if (! Environment::isMac()) {
            exec("xdg-open {$filePath}");
            return;
        }

        if ($this->editor()) {
            exec(sprintf('"%s" "%s"',
                $this->editor(),
                $filePath
            ));
            return;
        }

        exec("open {$filePath}");
    }

    public function editor()
    {
        return app('console')->option('editor');
    }
}
