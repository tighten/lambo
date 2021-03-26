<?php

namespace App\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class SilenceNpm
{
    private $packageJsonPath;
    private $backupPackageJsonPath;

    public function __construct()
    {
        $this->packageJsonPath = config('lambo.store.project_path') . '/package.json';
        $this->backupPackageJsonPath = config('lambo.store.project_path') . '/package-original.json';
    }

    public function silence()
    {
        File::copy($this->packageJsonPath, $this->backupPackageJsonPath);
        File::replace($this->packageJsonPath, $this->getSilentPackageJson($this->packageJsonPath));
    }

    public function unsilence()
    {
        File::move($this->backupPackageJsonPath, $this->packageJsonPath);
    }

    private function getSilentPackageJson(string $originalPackageJson)
    {
        $packageJson = json_decode(File::get($originalPackageJson), true);
        $silentDevelopmentCommand = str_replace('--progress', '--no-progress', Arr::get($packageJson, 'scripts.development'));
        Arr::set($packageJson, 'scripts.development', $silentDevelopmentCommand);

        return json_encode($packageJson, JSON_UNESCAPED_SLASHES);
    }
}
