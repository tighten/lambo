<?php

namespace App\Actions;

use App\InteractsWithLamboConfig;
use Illuminate\Support\Facades\File;

class EditConfig
{
    use InteractsWithLamboConfig;

    public function __invoke()
    {
        $this->createOrEditConfigFile("config", File::get(base_path('stubs/config.stub')));
    }
}
