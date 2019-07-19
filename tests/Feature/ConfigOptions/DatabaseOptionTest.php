<?php

namespace App\Feature;

use App\Facades\OptionManager;
use Tests\TestCase;

class DatabaseOptionTest extends TestCase
{
    /** @test */
    function setting_the_database_config_at_runtime()
    {
        $this->artisan('new blog --database=sqlite');

        $option = OptionManager::getOption('database')->getOptionValue();

        $this->assertEquals('sqlite', $option);
    }
}
