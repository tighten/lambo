<?php

namespace App\Actions;

use App\Support\BaseAction;

class OpenEditor extends BaseAction
{
    /**
     * Opens the project in the configured editor.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $directory = config('lambo.store.project_path');

        $editor = config('lambo.config.editor');

        if ($editor !== false) {
            $this->console->info("Opening project with editor: {$editor}");
            $this->shell->inDirectory($directory, "{$editor} .");
        }
    }
}
