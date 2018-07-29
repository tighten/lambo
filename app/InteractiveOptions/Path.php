<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use Illuminate\Support\Facades\File;
use App\Support\BaseInteractiveOption;

class Path extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'path';

    /**
     * Performs the option interactively.
     *
     * @param NewCommand $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $question = 'New path for installation? (empty for current working dir)';

        $answer = $console->ask($question, 'cwd');

        if ($answer === 'cwd') {
            $this->value        = $console->currentWorkingDir;
        } elseif (!$this->pathIsDirectory($answer, $console->currentWorkingDir)) {
            $this->value        = null;
            $this->message      = "Provided path [{$answer}] is not a directory. Path config value wasn't changed.";
            $this->messageLevel = 'error';
        }

        return $this;
    }

    /**
     * Checks if the given path is a directory.
     *
     * @param $path
     * @param $cwd
     * @return bool
     */
    protected function pathIsDirectory($path, $cwd): bool
    {
        if (starts_with($path, '~')) {
            // Path starts with '~', so it's relative to the HOME folder
            $installPath = str_replace('~', $_SERVER['HOME'], $path);
        } elseif (starts_with($path, '/')) {
            // Path starts with '~', so it's an absolute path
            $installPath = $path;
        } else {
            // Path is relative to the working dir
            $installPath = str_finish($cwd, '/') . $path;
        }

        return File::isDirectory($installPath);
    }
}
