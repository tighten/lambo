<?php

namespace Tests\Unit;

use App\Commands\NewCommand;
use App\Configuration\CommandLineConfiguration;
use App\Configuration\SavedConfiguration;
use App\Configuration\SetConfig;
use App\Configuration\ShellConfiguration;
use App\ConsoleWriter;
use App\LamboException;
use Illuminate\Support\Facades\File;
use Tests\Feature\LamboTestEnvironment;
use Tests\TestCase;

class SetConfigTest extends TestCase
{
    use LamboTestEnvironment;

    /** @test */
    function it_sets_the_top_level_domain()
    {
        File::shouldReceive('isFile')
            ->with(config('home_dir') . '/.config/valet/config.json')
            ->once()
            ->andReturnTrue()
            ->globally()
            ->ordered();

        File::shouldReceive('get')
            ->with(config('home_dir') . '/.config/valet/config.json')
            ->once()
            ->andReturn('{"tld": "mytld"}')
            ->globally()
            ->ordered();

        (new SetConfig(
            $this->mock(CommandLineConfiguration::class),
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))(['tld' => null]);

        $this->assertEquals('mytld', config('lambo.store.tld'));
    }

    /** @test */
    function it_sets_the_top_level_domain_using_legacy_valet_config()
    {
        File::shouldReceive('isFile')
            ->with(config('home_dir') . '/.config/valet/config.json')
            ->once()
            ->andReturnFalse()
            ->globally()
            ->ordered();

        File::shouldReceive('isFile')
            ->once()
            ->with(config('home_dir') . '/.valet/config.json')
            ->andReturnTrue()
            ->globally()
            ->ordered();

        File::shouldReceive('get')
            ->with(config('home_dir') . '/.valet/config.json')
            ->once()
            ->andReturn('{"domain": "mytld"}')
            ->globally()
            ->ordered();

        (new SetConfig(
            $this->mock(CommandLineConfiguration::class),
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))(['tld' => null]);

        $this->assertEquals('mytld', config('lambo.store.tld'));
    }

    /** @test */
    function it_throws_a_LamboException_if_valet_config_is_missing()
    {
        File::shouldReceive('isFile')
            ->with(config('home_dir') . '/.config/valet/config.json')
            ->once()
            ->andReturnFalse()
            ->globally()
            ->ordered();

        File::shouldReceive('isFile')
            ->once()
            ->with(config('home_dir') . '/.valet/config.json')
            ->andReturnFalse()
            ->globally()
            ->ordered();

        $this->expectException(LamboException::class);

        (new SetConfig(
            $this->mock(CommandLineConfiguration::class),
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))(['tld' => null]);
    }

    /** @test */
    function it_prioritises_command_line_configuration()
    {
        $this->withValetTld();

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->testKey = 'command-line-parameter';

        $savedConfiguration = $this->mock(SavedConfiguration::class);
        $savedConfiguration->testKey = 'saved-config-parameter';

        $shellConfiguration = $this->mock(ShellConfiguration::class);
        $shellConfiguration->testKey = 'shell-environment-parameter';

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration,
            app(ConsoleWriter::class)
        ))([
            'testKey' => 'default',
        ]);

        $this->assertEquals('command-line-parameter', config('lambo.store.testKey'));
    }

    /** @test */
    function it_prioritises_saved_configuration_over_shell_environment_configuration()
    {
        $this->withValetTld();

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->testKey = null;

        $savedConfiguration = $this->mock(SavedConfiguration::class);
        $savedConfiguration->testKey = 'saved-config-parameter';

        $shellConfiguration = $this->mock(ShellConfiguration::class);
        $shellConfiguration->testKey = 'shell-environment-parameter';

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration,
            app(ConsoleWriter::class)
        ))([
            'testKey' => 'default',
        ]);

        $this->assertEquals('saved-config-parameter', config('lambo.store.testKey'));
    }

    /** @test */
    function it_prioritises_shell_environment_over_default_configuration()
    {
        $this->withValetTld();

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->testKey = null;

        $savedConfiguration = $this->mock(SavedConfiguration::class);
        $savedConfiguration->testKey = null;

        $shellConfiguration = $this->mock(ShellConfiguration::class);
        $shellConfiguration->testKey = 'shell-environment-parameter';

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration,
            app(ConsoleWriter::class)
        ))([
            'testKey' => 'default',
        ]);

        $this->assertEquals('shell-environment-parameter', config('lambo.store.testKey'));
    }

    /** @test */
    function it_uses_a_default_configuration()
    {
        $this->withValetTld();

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->testKey = null;

        $savedConfiguration = $this->mock(SavedConfiguration::class);
        $savedConfiguration->testKey = null;

        $shellConfiguration = $this->mock(ShellConfiguration::class);
        $shellConfiguration->testKey = null;

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration,
            app(ConsoleWriter::class)
        ))([
            'testKey' => 'default',
        ]);

        $this->assertEquals('default', config('lambo.store.testKey'));
    }

    /** @test */
    function it_replaces_tilda_in_root_path()
    {
        config(['home_dir' => '/home/user']);

        $this->withValetTld();

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->root_path = '~/path/from/command/line';

        (new SetConfig(
            $commandLineConfiguration,
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))(['root_path' => getcwd()]);

        $this->assertEquals('/home/user/path/from/command/line', config('lambo.store.root_path'));

        config(['lambo.store' => []]);

        $savedConfiguration = $this->mock(SavedConfiguration::class);
        $savedConfiguration->root_path = '~/path/from/saved/configuration';

        (new SetConfig(
            $this->mock(CommandLineConfiguration::class),
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))(['root_path' => getcwd()]);

        $this->assertEquals('/home/user/path/from/saved/configuration', config('lambo.store.root_path'));
    }

    /** @test */
    function it_replaces_hyphens_with_underscores_in_database_names()
    {
        $this->withValetTld();
        config(['home_dir' => '/home/user']);

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->project_name = 'foo';
        $commandLineConfiguration->database_name = 'h-y-p-h-e-n-s';

        (new SetConfig(
            $commandLineConfiguration,
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))([
            'root_path' => getcwd(),
            'project_name' => null,
            'database_name' => null,
        ]);

        $this->assertEquals('h_y_p_h_e_n_s', config('lambo.store.database_name'));
    }

    /** @test */
    function it_sets_the_project_url()
    {
        $this->withValetTld('test-domain');

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->project_name = 'foo';

        (new SetConfig(
            $commandLineConfiguration,
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))([
            'command' => NewCommand::class,
            'tld' => null,
            'project_name' => null,
            'root_path' => '/some/path',
            'valet_secure' => false,
        ]);

        $this->assertEquals('http://foo.test-domain', config('lambo.store.project_url'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->valet_secure = true;

        (new SetConfig(
            $commandLineConfiguration,
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))([
            'command' => NewCommand::class,
            'tld' => null,
            'project_name' => null,
            'root_path' => '/some/path',
            'valet_secure' => false,
        ]);

        $this->assertEquals('https://foo.test-domain', config('lambo.store.project_url'));
    }

    /** @test */
    function it_sets_the_project_name()
    {
        $this->withValetTld();

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->project_name = 'foo';

        (new SetConfig(
            $commandLineConfiguration,
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))(['project_name' => null]);

        $this->assertEquals('foo', config('lambo.store.project_name'));
    }

    /** @test */
    function it_sets_the_project_path()
    {
        $this->withValetTld();
        config(['home_dir' => '/home/user']);

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->root_path = '/path/from/command/line';
        $commandLineConfiguration->project_name = 'my-project';

        (new SetConfig(
            $commandLineConfiguration,
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class)
        ))([
            'command' => NewCommand::class,
            'root_path' => getcwd(),
            'project_name' => null,
        ]);

        $this->assertEquals('/path/from/command/line/my-project', config('lambo.store.project_path'));
    }

    /** @test */
    function it_sets_the_create_database_configuration()
    {
        $this->withValetTld();

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->create_database = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'create_database' => false,
        ]);
        $this->assertTrue(config('lambo.store.create_database'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->create_database = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'create_database' => false,
        ]);
        $this->assertTrue(config('lambo.store.create_database'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->create_database = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'create_database' => false,
        ]);
        $this->assertFalse(config('lambo.store.create_database'));
    }

    /** @test */
    function it_sets_the_migrate_database_configuration()
    {
        $this->withValetTld();

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->migrate_database = false;
        $commandLineConfiguration->inertia = false;
        $commandLineConfiguration->livewire = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'migrate_database' => false,
            'inertia' => false,
            'livewire' => false,
        ]);
        $this->assertTrue(config('lambo.store.migrate_database'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->migrate_database = true;
        $commandLineConfiguration->inertia = false;
        $commandLineConfiguration->livewire = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'migrate_database' => false,
            'inertia' => false,
            'livewire' => false,
        ]);
        $this->assertTrue(config('lambo.store.migrate_database'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->migrate_database = false;
        $commandLineConfiguration->inertia = false;
        $commandLineConfiguration->livewire = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'migrate_database' => false,
            'inertia' => false,
            'livewire' => false,
        ]);
        $this->assertFalse(config('lambo.store.migrate_database'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->migrate_database = false;
        $commandLineConfiguration->inertia = true;
        $commandLineConfiguration->livewire = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'migrate_database' => false,
            'inertia' => false,
            'livewire' => false,
        ]);
        $this->assertTrue(config('lambo.store.migrate_database'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->migrate_database = false;
        $commandLineConfiguration->inertia = false;
        $commandLineConfiguration->livewire = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'migrate_database' => false,
            'inertia' => false,
            'livewire' => false,
        ]);
        $this->assertTrue(config('lambo.store.migrate_database'));
    }

    /** @test */
    function it_sets_the_valet_link_configuration()
    {
        $this->withValetTld('test-domain');
        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->valet_link = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'valet_link' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_link'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_link = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'valet_link' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_link'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_link = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'valet_link' => false,
        ]);
        $this->assertFalse(config('lambo.store.valet_link'));
    }

    /** @test */
    function it_sets_the_valet_secure_configuration()
    {
        $this->withValetTld('test-domain');
        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->valet_secure = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => true,
            'valet_secure' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_secure'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_secure = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'valet_secure' => true,
        ]);
        $this->assertTrue(config('lambo.store.valet_secure'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_secure = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class), app(ConsoleWriter::class)))([
            'tld' => null,
            'full' => false,
            'valet_secure' => false,
        ]);
        $this->assertFalse(config('lambo.store.valet_secure'));
    }
}
