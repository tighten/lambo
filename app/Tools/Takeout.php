<?php

namespace App\Tools;

use App\Shell;
use Illuminate\Support\Str;
use Symfony\Component\Process\ExecutableFinder;

class Takeout
{
    private $shell;
    private $finder;
    private $filterList;

    public function __construct(Shell $shell, ExecutableFinder $finder)
    {
        $this->shell = $shell;
        $this->finder = $finder;
    }

    public function list(): array
    {
        $process = $this->shell->execQuietly('takeout list --json');

        $containers = json_decode($process->getOutput(), true);

        if (empty($this->filterList)) {
            return $containers;
        }

        return collect($containers)->filter(function ($container) {
            return Str::of($container['names'])->contains($this->filterList);
        })->values()->all();
    }

    public function find()
    {
        return ! is_null($this->finder->find('takeout'));
    }

    public function start(string $container)
    {
        $process = $this->shell->execQuietly("takeout start {$container}");
        return $process->getExitCode() === 0;
    }

    public function only(array $filterList = [])
    {
        $this->filterList = $filterList;
        return $this;
    }
}
