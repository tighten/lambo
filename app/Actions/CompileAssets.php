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

        $this->squashProgress();

        $this->logStep('Compiling project assets');

        $process = $this->shell->execInProject("npm run dev-no-progress {$this->extraOptions()}");

        $this->abortIf(! $process->isSuccessful(), 'Compilation of project assets did not complete successfully', $process);

        $this->info('Project assets compiled successfully.');
    }

    public function extraOptions()
    {
        return config('lambo.store.show-output') ? '' : '--silent';
    }

    private function squashProgress()
    {
        $path = config('lambo.store.project_path') . '/package.json';
        $packageJson = json_decode(File::get($path), true);
        $compileCommand = str_replace('--progress', '--no-progress', Arr::get($packageJson, 'scripts.development'));
        $newPackageJson = Arr::add($packageJson, 'scripts.dev-no-progress', $compileCommand);
        File::replace($path, json_encode($newPackageJson, JSON_PRETTY_PRINT));
    }
}
