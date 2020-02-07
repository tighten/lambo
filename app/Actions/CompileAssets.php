<?php

namespace App\Actions;

use App\Shell\Shell;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class CompileAssets
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (! config('lambo.store.mix')) {
            return;
        }

        $this->addSilentDevScript();

        $this->logStep('Compiling project assets');

        $process = $this->shell->execInProject("npm run dev {$this->extraOptions()}");

        $this->abortIf(! $process->isSuccessful(), 'Compilation of project assets did not complete successfully', $process);

        $this->removeSilentDevScript();

        $this->info('Project assets compiled successfully.');
    }

    public function extraOptions()
    {
        return config('lambo.store.show-output') ? '' : '--silent';
    }

    public function addSilentDevScript()
    {
        $originalPackageJson = config('lambo.store.project_path') . '/package.json';
        File::copy($originalPackageJson, config('lambo.store.project_path') . '/package-original.json');
        File::replace($originalPackageJson, $this->silentPackageJson($originalPackageJson));
    }

    public function silentPackageJson($originalPackageJsonPath)
    {
        $packageJson = json_decode(File::get($originalPackageJsonPath), true);
        $silentDevelopmentCommand = str_replace('--progress', '--no-progress', Arr::get($packageJson, 'scripts.development'));
        Arr::set($packageJson, 'scripts.development', $silentDevelopmentCommand);
        return json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function removeSilentDevScript()
    {
        $packageJson = config('lambo.store.project_path') . '/package.json';
        $originalPackageJson = config('lambo.store.project_path') . '/package-original.json';
        File::move($originalPackageJson, $packageJson);
    }
}
