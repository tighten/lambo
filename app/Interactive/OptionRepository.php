<?php

namespace App\Interactive;

use App\InteractiveOptions\Path;
use Illuminate\Support\Collection;
use App\InteractiveOptions\Editor;
use App\InteractiveOptions\Release;
use App\InteractiveOptions\CommitMessage;

class OptionRepository
{
    /**
     * The interactive options
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return collect([
            [
                'key'   => 'editor',
                'label' => 'Editor - to open project after installation',
                'class' => Editor::class,
            ],
            [
                'key'   => 'message',
                'label' => 'The commit message',
                'class' => CommitMessage::class,
            ],
            [
                'key'   => 'path',
                'label' => 'Installation path',
                'class' => Path::class,
            ],
            [
                'key'   => 'release',
                'label' => 'The Laravel branch to use, dev or stable',
                'class' => Release::class,
            ],
        ]);
    }
}
