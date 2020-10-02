<?php

namespace Tests\Feature;

use App\Actions\UpgradeSavedConfiguration;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UpgradeSavedConfigurationTest extends TestCase
{
    /** @test */
    function it_comments_out_old_configuration_parameters()
    {
        $fixturePath = base_path('tests/Feature/Fixtures') . '/.lambo';

        $oldConfiguration = File::get("{$fixturePath}/old_configuration");
        $upgradedConfiguration = app(UpgradeSavedConfiguration::class)->upgrade($oldConfiguration);

        $this->assertEquals(
            trim(File::get("{$fixturePath}/commented_configuration")),
            trim($upgradedConfiguration)
        );
    }
}
