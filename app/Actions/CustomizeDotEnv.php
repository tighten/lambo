<?php

namespace App\Actions;

use Illuminate\Support\Facades\File;

class CustomizeDotEnv
{
    public function __invoke()
    {
        $filePath = config('lambo.store.project_path') . '/' . '.env.example';

        $file = File::get($filePath);

        dd($file); // test --how do we get them as array of lines

        foreach ($this->replaceArray() as $key => $value) {
            // make the change
        }

        // save the change
    }

    public function replaceArray()
    {
        return [
            'APP_NAME' => config('lambo.store.project_name'),
            'APP_URL' => config('lambo.store.project_url'),
            'DB_DATABASE' => $this->databaseify(config('lambo.store.project_name')),
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => null,
        ];
    }

    public function databaseify($name)
    {
        // @todo
        return $name;
    }
}
