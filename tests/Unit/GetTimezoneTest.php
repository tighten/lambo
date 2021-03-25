<?php

namespace Tests\Unit;

use App\Helpers\GetTimezone;
use IntlTimeZone;
use Tests\TestCase;

class GetTimezoneTest extends TestCase
{
    /** @test */
    function it_uses_the_timezone_configured_in_php_ini()
    {
        $availableTimezones = array_diff(
            ['America/New_York', 'Europe/London'],
            [IntlTimeZone::createDefault()->getId()]
        );
        $expectedTimezone = array_pop($availableTimezones);

        ini_set('date.timezone', $expectedTimezone);
        $this->assertEquals($expectedTimezone, app(GetTimezone::class)());
    }

    /** @test */
    function it_uses_the_timezone_of_the_host_operating_system()
    {
        ini_set('date.timezone', '');
        $expectedTimezone = IntlTimeZone::createDefault()->getId();
        $this->assertEquals($expectedTimezone, app(GetTimezone::class)());
    }
}
