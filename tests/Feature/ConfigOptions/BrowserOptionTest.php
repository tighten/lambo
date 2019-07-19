<?php

namespace App\Feature;

use App\Facades\OptionManager;
use Tests\TestCase;

class BrowserOptionTest extends TestCase
{
    /** @test */
    function setting_the_browser_at_runtime()
    {
        $this->artisan('new blog --browser="/Applications/Google Chrome.app"');

        $browser = OptionManager::getOption('browser')->getOptionValue();

        $this->assertEquals('/Applications/Google Chrome.app', $browser);
    }
}
