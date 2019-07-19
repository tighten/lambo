<?php

namespace App\Feature;

use App\Facades\OptionManager;
use Tests\TestCase;

class AuthOptionTest extends TestCase
{
    /** @test */
    function setting_the_auth_config_at_runtime()
    {
        $this->artisan('new blog --auth');

        $option = OptionManager::getOption('auth')->getOptionValue();

        $this->assertTrue($option);
    }
}
