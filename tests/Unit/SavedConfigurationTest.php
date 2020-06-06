<?php

namespace Tests\Unit;

use App\Configuration\SavedConfiguration;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SavedConfigurationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('home_dir', base_path('tests/Feature/Fixtures'));
        Config::set('config_dir', '.lambo');
    }

    /** @test */
    function it_gets_a_saved_configuration_value()
    {
        $savedConfiguration = new SavedConfiguration([
            'CONFIGURATION_OPTION' => 'genericOption',
        ]);

        $this->assertEquals('foo', $savedConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_saved_configuration_option_is_missing()
    {
        $savedConfiguration = new SavedConfiguration([
            'MISSING_CONFIGURATION_OPTION' => 'genericOption',
        ]);

        $this->assertNull($savedConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_saved_configuration_option_has_no_value()
    {
        $savedConfiguration = new SavedConfiguration([
            'CONFIGURATION_OPTION_NO_VALUE' => 'genericOption',
        ]);

        $this->assertNull($savedConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_saved_configuration_option_is_empty()
    {
        $savedConfiguration = new SavedConfiguration([
            'CONFIGURATION_OPTION_EMPTY_VALUE' => 'genericOption',
        ]);

        $this->assertNull($savedConfiguration->genericOption);
    }

    /** test */
    function it_returns_null_when_the_configuration_file_does_not_exist()
    {
        Config::set('config_file', 'non-existent-configuration_file');

        $savedConfiguration = new SavedConfiguration([
            'CONFIGURATION_VALUE' => 'genericOption',
        ]);

        $this->assertNull((new SavedConfiguration)->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_non_existent_property_is_requested()
    {
        $savedConfiguration = new SavedConfiguration([]);
        $this->assertNull($savedConfiguration->foo);
    }

    /** @test */
    function it_casts_strings_to_booleans()
    {
        $savedConfiguration = new SavedConfiguration([
            'BOOLEAN_OPTION_1' => 'booleanOption1',
            'BOOLEAN_OPTION_TRUE' => 'booleanOptionTrue',
            'BOOLEAN_OPTION_ON' => 'booleanOptionOn',
            'BOOLEAN_OPTION_YES' => 'booleanOptionYes',
            'BOOLEAN_OPTION_0' => 'booleanOption0',
            'BOOLEAN_OPTION_FALSE' => 'booleanOptionFalse',
            'BOOLEAN_OPTION_OFF' => 'booleanOptionOff',
            'BOOLEAN_OPTION_NO' => 'booleanOptionNo',
        ]);
        $this->assertTrue($savedConfiguration->booleanOption1);
        $this->assertTrue($savedConfiguration->booleanOptionTrue);
        $this->assertTrue($savedConfiguration->booleanOptionOn);
        $this->assertTrue($savedConfiguration->booleanOptionYes);
        $this->assertFalse($savedConfiguration->booleanOption0);
        $this->assertFalse($savedConfiguration->booleanOptionFalse);
        $this->assertFalse($savedConfiguration->booleanOptionOff);
        $this->assertFalse($savedConfiguration->booleanOptionNo);
    }
}
