<?php

namespace App\Actions;

use App\InteractsWithLamboConfig;
use App\Presets\BasePreset;
use Illuminate\Support\Str;

class RunPresets
{
    use LamboAction, InteractsWithLamboConfig;

    public $presets = []; // Array of stdClass objects, each with "preset" and "parameters" keys

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
            // construct
            $preset = $this->getPresetByShortName($preset->preset);

            // run before
            $preset->baseBefore();

            // run run
            $preset->baseRun();

            // run after
            $preset->baseAfter();

            dd($preset);
        }
    }

    public function getPresetByShortName(string $shortName): BasePreset
    {
        $className = $this->getPresetClassName($shortName);

        // look for the pre-made class
        $fqcn = "App\\Presets\\Premade\\{$className}";

        if (class_exists($fqcn)) {
            return app($fqcn);
        }

        throw new \Exception('Cannot resolve preset: ' . $shortName);

        // look for the hand-made local
        // @todo something like "custom/telescope"

        // look for the composer-loaded class
        // @todo something like "nunomaduro/telescope"
    }

    public function getPresetClassName(string $presetShortName): string
    {
        return Str::studly($presetShortName);
    }
}
