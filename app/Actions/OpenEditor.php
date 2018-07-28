<?php

namespace App\Actions;

use App\Support\BaseAction;

class OpenEditor extends BaseAction
{
    public function __invoke()
    {
        $directory = config('lambo-store.project_path');

        $editor = config('lambo.editor');

        if ($editor !== false) {
            $this->console->info("Opening project with editor: {$editor}");
            $this->shell->inDirectory($directory, "{$editor} .");
        }
    }
}
