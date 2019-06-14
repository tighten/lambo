<?php

namespace App\Feature;

use App\Facades\OptionManager;
use Tests\TestCase;

class NodeTest extends TestCase
{
    /** @test */
    function setting_the_node_config_at_runtime()
    {
        $this->artisan('new blog --node=npm');

        $option = OptionManager::getOption('node')->getOptionValue();

        $this->assertEquals('npm', $option);
    }
}
