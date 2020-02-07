<?php

namespace App\Actions;

use App\InteractsWithLamboConfig;
use Illuminate\Support\Facades\File;

class EditAfter
{
    use InteractsWithLamboConfig;

    public function __invoke()
    {
        $this->createOrEditConfigFile("after", File::get(base_path('stubs/after.stub')));
    }
}
