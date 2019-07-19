<?php

namespace App\Feature;

use App\Facades\OptionManager;
use Tests\TestCase;

class PathOptionTest extends TestCase
{
    /** @test */
    function setting_the_install_path_at_runtime()
    {
        $this->artisan('new blog --path="~/Sites"');

        $path = OptionManager::getOption('path')->getOptionValue();

        $this->assertEquals('~/Sites', $path);
    }
}
