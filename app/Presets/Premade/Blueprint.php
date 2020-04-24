<?php

namespace App\Presets\Premade;

use App\Presets\BasePreset;
use Illuminate\Support\Facades\File;

class Blueprint extends BasePreset
{
    public $composerDevRequires = [
        'laravel-shift/blueprint' => '^1.6',
        'jasonmccreary/laravel-test-assertions' => '^1.0',
    ];

    public function run()
    {
        $stub = File::copy(
            base_path('app/Presets/Premade/stubs/blueprint-draft.yaml'),
            config('lambo.store.project_path') . '/draft.yaml'
        );
    }
}
