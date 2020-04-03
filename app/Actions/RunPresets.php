<?php

namespace App\Actions;

use App\InteractsWithLamboConfig;
use Illuminate\Support\Str;

class RunPresets
{
    use LamboAction, InteractsWithLamboConfig;

    public $presets = []; // Array of stdClass objects with "preset" and "parameters"

    public function __construct()
    {
        $this->presets = $this->presetsPassed();
    }

    public function presetsPassed()
    {
        return collect(explode('|', config('lambo.store.presets')))
            ->map(function ($preset) {
                $parameters = Str::contains($preset, ':')
                    ? explode(',', Str::after($preset, ':'))
                    : [];

                return (object) [
                    'preset' => Str::before($preset, ':'),
                    'parameters' => $parameters,
                ];
            })->toArray();
    }

    public function __invoke()
    {
        foreach ($this->presets as $preset) {
            dd('!@todo!');
        }
    }
}
