<?php

namespace App\Actions;

use Illuminate\Support\Facades\File;

class CreateOrEditConfig
{
    public function __invoke()
    {
        $configPath = config('home_dir') . '/.lambo';
        $configFilePath = "{$configPath}/config";

        if (File::exists($configFilePath)) {
            app('console')->info("Opening existing config file: {$configFilePath}");
            $this->editConfig($configFilePath);
            return 0;
        }

        if (!File::exists($configPath)) {
            File::makeDirectory($configPath);
        }

        File::put("$configFilePath", $this->configFileTemplate());
        app('console')->info("Opening new config file {$configFilePath}");
        $this->editConfig($configFilePath);
    }

    public function isMac()
    {
        return PHP_OS === 'Darwin';
    }

    public function editor()
    {
        return app('console')->option('editor');
    }

    protected function editConfig(string $configFilePath)
    {
        if (!$this->isMac()) {
            exec("xdg-open {$configFilePath}");
            return;
        }

        if ($this->editor()) {
            exec(sprintf('"%s" "%s"',
                $this->editor(),
                $configFilePath
            ));
            return;
        }

        exec("open {$configFilePath}");
    }

    private function configFileTemplate(): string
    {
        return <<<'CONTENTS'
PROJECTPATH="."
MESSAGE="Initial commit."
QUIET=false
DEVELOP=false
AUTH=false
NODE=false
CODEEDITOR=""
BROWSER=""
LINK=false
SECURE=false
FRONTEND="vue"
CREATE_DATABASE=false
DB_USERNAME="root"
DB_PASSWORD=""
CONTENTS;
    }
}
