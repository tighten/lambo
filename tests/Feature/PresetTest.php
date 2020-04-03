<?php

namespace Tests\Feature;

use App\Presets\Premade\Telescope;
use App\Shell\Shell;
use Tests\TestCase;

class PresetTest extends TestCase
{
    /** @test */
    function it_generates_composer_require_strings()
    {
        $preset = app(Telescope::class);
        $preset->composerRequires = [
            'laravel/telescope' => '~1.0',
            'tightenco/tlint' => '~1.0',
        ];

        $this->assertEquals(
            'composer require laravel/telescope:"~1.0" tightenco/tlint:"~1.0"',
            $preset->buildComposerRequireString()
        );
    }

    /** @test */
    function it_requires_all_packages_in_composer_requires_array()
    {
        $this->markTestIncomplete();

        $shell = $this->mock(Shell::class);

        $preset = app(Telescope::class);
        $preset->composerRequires = [
            'laravel/telescope' => '~1.0',
        ];

        // assert that 'composer require laravel/telescope:"~1.0"' was run
    }
}
