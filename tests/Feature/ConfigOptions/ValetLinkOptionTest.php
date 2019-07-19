<?php

namespace App\Feature;

use App\Facades\OptionManager;
use Tests\TestCase;

class ValetLinkOptionTest extends TestCase
{
    /** @test */
    function setting_the_valet_link_config_at_runtime()
    {
        $this->artisan('new blog --link');

        $option = OptionManager::getOption('link')->getOptionValue();

        $this->assertTrue($option);
    }
}
