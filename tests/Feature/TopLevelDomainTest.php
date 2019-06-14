<?php

namespace App\Feature;

use App\Facades\OptionManager;
use Tests\TestCase;

class TopLevelDomainTest extends TestCase
{
    /** @test */
    function setting_the_tld_config_at_runtime()
    {
        $this->artisan('new blog --tld=.local');

        $option = OptionManager::getOption('tld')->getOptionValue();

        $this->assertEquals('.local', $option);
    }
}
