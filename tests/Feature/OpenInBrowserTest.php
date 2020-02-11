<?php

namespace Tests\Feature;

use App\Actions\OpenInBrowser;
use App\Environment;
use App\Shell\Shell;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class OpenInBrowserTest extends TestCase
{
    /** @test */
    function it_opens_the_project_homepage_using_the_specified_browser_on_mac()
    {
        $this->fakeLamboConsole();
        $environment = $this->mock(Environment::class);
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.browser', '/Applications/my/browser.app');
        Config::set('lambo.store.project_url', 'http://my-project.test');

        $environment->shouldReceive('isMac')
            ->once()
            ->andReturn(true);

        $shell->shouldReceive('execInProject')
            ->once()
            ->with('open -a "/Applications/my/browser.app" "http://my-project.test"');

        app(OpenInBrowser::class)();
    }

    /** @test */
    function it_opens_the_project_homepage_using_valet_open_when_no_browser_is_specified_on_mac()
    {
        $this->fakeLamboConsole();
        $environment = $this->mock(Environment::class);
        $shell = $this->mock(Shell::class);

        $this->assertEmpty(Config::get('lambo.store.browser'));

        $environment->shouldReceive('isMac')
            ->once()
            ->andReturn(true);

        $shell->shouldReceive('execInProject')
            ->once()
            ->with('valet open');

        app(OpenInBrowser::class)();
    }

    /** @test */
    function it_uses_valet_open_when_not_running_on_mac()
    {
        $this->fakeLamboConsole();
        $environment = $this->mock(Environment::class);
        $shell = $this->mock(Shell::class);

        $environment->shouldReceive('isMac')
            ->once()
            ->andReturn(false);

        $shell->shouldReceive('execInProject')
            ->once()
            ->with('valet open');

        app(OpenInBrowser::class)();
    }

    /** @test */
    function it_ignores_the_specified_browser_when_not_running_on_mac()
    {
        $this->fakeLamboConsole();
        $environment = $this->mock(Environment::class);
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.browser', '/path/to/a/browser');
        Config::set('lambo.store.project_url', 'http://my-project.test');

        $environment->shouldReceive('isMac')
            ->once()
            ->andReturn(false);

        $shell->shouldReceive('execInProject')
            ->once()
            ->with('valet open');

        app(OpenInBrowser::class)();
    }
}
