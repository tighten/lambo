<?php

namespace Tests\Unit;

use App\Actions\InstallBreeze;
use App\Actions\InstallJetstream;
use App\Commands\NewCommand;
use App\Configuration\CommandLineConfiguration;
use App\Configuration\SavedConfiguration;
use App\Configuration\SetConfig;
use App\Configuration\ShellConfiguration;
use App\ConsoleWriter;
use App\LamboException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use Tests\Feature\LamboTestEnvironment;
use Tests\TestCase;
use Tests\Unit\Fakes\FakeInput;

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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
        ))(['root_path' => getcwd()]);

        $this->assertEquals('/home/user/path/from/command/line', config('lambo.store.root_path'));

        config(['lambo.store' => []]);

        $savedConfiguration = $this->mock(SavedConfiguration::class);
        $savedConfiguration->root_path = '~/path/from/saved/configuration';

        (new SetConfig(
            $this->mock(CommandLineConfiguration::class),
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
            app(ConsoleWriter::class),
            new FakeInput()
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
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
            'tld' => null,
            'full' => false,
            'create_database' => false,
        ]);
        $this->assertTrue(config('lambo.store.create_database'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->create_database = true;
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
            'tld' => null,
            'full' => false,
            'create_database' => false,
        ]);
        $this->assertTrue(config('lambo.store.create_database'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->create_database = false;
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
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
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
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
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
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
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
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
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
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
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
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
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
            'tld' => null,
            'full' => false,
            'valet_link' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_link'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_link = true;
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
            'tld' => null,
            'full' => false,
            'valet_link' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_link'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_link = false;
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
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
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
            'tld' => null,
            'full' => true,
            'valet_secure' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_secure'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_secure = true;
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
            'tld' => null,
            'full' => false,
            'valet_secure' => true,
        ]);
        $this->assertTrue(config('lambo.store.valet_secure'));

        config(['lambo.store' => []]);

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_secure = false;
        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $this->mock(ShellConfiguration::class),
            app(ConsoleWriter::class),
            new FakeInput()
        ))([
            'tld' => null,
            'full' => false,
            'valet_secure' => false,
        ]);
        $this->assertFalse(config('lambo.store.valet_secure'));
    }

    /**
     * @test
     * @group front-end-scaffolding
     */
    function it_sets_the_breeze_starter_kit_configuration()
    {
        $this->withValetTld('test-domain');

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);

        foreach (InstallBreeze::VALID_STACKS as $stack) {
            config(['lambo.store' => []]);
            (new SetConfig(
                $commandLineConfiguration,
                $this->mock(SavedConfiguration::class),
                $this->mock(ShellConfiguration::class),
                app(ConsoleWriter::class),
                new FakeInput([
                    'breeze' => $stack,
                ])
            ))([
                'breeze' => false,
            ]);

            static::assertEquals($stack, config('lambo.store.breeze'));
            static::assertFalse(config('lambo.store.jetstream'));

            if (in_array('--debug', $_SERVER['argv'], true)) {
                $this->toSTDOUT(sprintf('[ Options ] -jetstream=%s', $stack));
                $this->toSTDOUT(sprintf('[ Config  ] lambo.store.jetstream => %s', config('lambo.store.jetstream') ? config('lambo.store.jetstream') : 'false'));
                $this->toSTDOUT(sprintf('[ Config  ] lambo.store.breeze => %s', config('lambo.store.breeze') ? config('lambo.store.breeze') : 'false'));
                $this->toSTDOUT('-------------');
            }
        }
    }

    /**
     * @test
     * @group front-end-scaffolding
     */
    function it_sets_the_jetstream_starter_kit_configuration()
    {
        $this->withValetTld('test-domain');

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);

        foreach (InstallJetstream::VALID_CONFIGURATIONS as $stack) {
            config(['lambo.store' => []]);
            (new SetConfig(
                $commandLineConfiguration,
                $this->mock(SavedConfiguration::class),
                $this->mock(ShellConfiguration::class),
                app(ConsoleWriter::class),
                new FakeInput([
                    'jetstream' => $stack,
                ])
            ))([
                'jetstream' => false,
            ]);

            static::assertEquals($stack, config('lambo.store.jetstream'));
            static::assertFalse(config('lambo.store.breeze'));

            if (in_array('--debug', $_SERVER['argv'], true)) {
                $this->toSTDOUT(sprintf('[ Options ] -jetstream=%s', $stack));
                $this->toSTDOUT(sprintf('[ Config  ] lambo.store.jetstream => %s', config('lambo.store.jetstream') ? config('lambo.store.jetstream') : 'false'));
                $this->toSTDOUT(sprintf('[ Config  ] lambo.store.breeze => %s', config('lambo.store.breeze') ? config('lambo.store.breeze') : 'false'));
                $this->toSTDOUT('-------------');
            }
        }
    }

    /**
     * @test
     * @group front-end-scaffolding
     */
    function it_ensures_only_one_starter_kit_is_configured()
    {
        $this->withValetTld('test-domain');

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);

        foreach (InstallBreeze::VALID_STACKS as $breezeStack) {
            foreach (InstallJetstream::VALID_CONFIGURATIONS as $jetstreamStack) {
                foreach (['None', 'Laravel Breeze', 'Laravel Jetstream',] as $stackChoice) {
                    $consoleWriter = $this->mock(ConsoleWriter::class, function (MockInterface $consoleWriter) use ($stackChoice) {
                        $consoleWriter->shouldReceive('newLine')->once()->globally()->ordered();
                        $consoleWriter->shouldReceive('note')->once()->globally()->ordered();
                        $consoleWriter->shouldReceive('choice')->once()->globally()->ordered()->andReturn($stackChoice);

                        $stackChoice === 'None'
                            ? $consoleWriter->shouldReceive('ok')->with('Skipping starter-kit installation.')->once()->globally()->ordered()
                            : $consoleWriter->shouldReceive('ok')->with("Using {$stackChoice}")->once()->globally()->ordered();
                    });

                    config(['lambo.store' => []]);
                    (new SetConfig(
                        $commandLineConfiguration,
                        $this->mock(SavedConfiguration::class),
                        $this->mock(ShellConfiguration::class),
                        $consoleWriter,
                        new FakeInput([
                            'jetstream' => $jetstreamStack,
                            'breeze' => $breezeStack,
                        ])
                    ))([
                        'breeze' => false,
                        'jetstream' => false,
                    ]);

                    switch ($stackChoice) {
                        case 'Laravel Breeze':
                            static::assertEquals($breezeStack, config('lambo.store.breeze'));
                            static::assertFalse(config('lambo.store.jetstream'));
                            break;
                        case 'Laravel Jetstream':
                            static::assertEquals($jetstreamStack, config('lambo.store.jetstream'));
                            static::assertFalse(config('lambo.store.breeze'));
                            break;
                        case 'None':
                            static::assertFalse(config('lambo.store.breeze'));
                            static::assertFalse(config('lambo.store.jetstream'));
                            break;
                    }

                    if (in_array('--debug', $_SERVER['argv'], true)) {
                        $this->toSTDOUT(sprintf('[ Options ] --breeze=%s --jetstream=%s', $breezeStack, $jetstreamStack));
                        $this->toSTDOUT(sprintf('[ Choice  ] %s', $stackChoice));
                        $this->toSTDOUT(sprintf('[ Config  ] lambo.store.jetstream => %s', config('lambo.store.jetstream') ? config('lambo.store.jetstream') : 'false'));
                        $this->toSTDOUT(sprintf('[ Config  ] lambo.store.breeze => %s', config('lambo.store.breeze') ? config('lambo.store.breeze') : 'false'));
                        $this->toSTDOUT('-------------');
                    }
                }
            }
        }
    }

    /**
     * @test
     * @group front-end-scaffolding
     */
    function it_asks_for_clarification_when_breeze_configuration_is_invalid()
    {
        $this->withValetTld('test-domain');

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);

        foreach ([null, '', 'invalid'] as $invalidStack) {
            foreach (array_keys(InstallBreeze::VALID_STACKS) as $stackChoice) {
                $consoleWriter = $this->mock(ConsoleWriter::class, function (MockInterface $consoleWriter) use ($stackChoice) {
                    $consoleWriter->shouldReceive('note')->once()->globally()->ordered();
                    $consoleWriter->shouldReceive('choice')->once()->globally()->ordered()
                        ->with(Mockery::type('string'), array_keys(InstallBreeze::VALID_STACKS))
                        ->andReturn($stackChoice);
                    $consoleWriter->shouldReceive('ok')->once()->globally()->ordered();
                });
                config(['lambo.store' => []]);

                (new SetConfig(
                    $commandLineConfiguration,
                    $this->mock(SavedConfiguration::class),
                    $this->mock(ShellConfiguration::class),
                    $consoleWriter,
                    new FakeInput([
                        'breeze' => $invalidStack,
                    ])
                ))([
                    'breeze' => false,
                ]);

                static::assertEquals(Str::lower($stackChoice), config('lambo.store.breeze'));
                static::assertFalse(config('lambo.store.jetstream'));

                if (in_array('--debug', $_SERVER['argv'], true)) {
                    $this->toSTDOUT(sprintf('[ Options ] %s', is_null($invalidStack) ? '--breeze' : "--breeze={$invalidStack}"));
                    $this->toSTDOUT(sprintf('[ Choice  ] %s', $stackChoice));
                    $this->toSTDOUT(sprintf('[ Config  ] lambo.store.jetstream => %s', config('lambo.store.jetstream') ? config('lambo.store.jetstream') : 'false'));
                    $this->toSTDOUT(sprintf('[ Config  ] lambo.store.breeze => %s', config('lambo.store.breeze') ? config('lambo.store.breeze') : 'false'));
                    $this->toSTDOUT('-------------');
                }
            }
        }
    }

    /**
     * @test
     * @group front-end-scaffolding
     */
    function it_asks_for_clarification_when_jetstream_configuration_is_invalid()
    {
        $this->withValetTld('test-domain');

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);

        foreach ([null, '', 'invalid'] as $invalidStack) {
            foreach (array_keys(InstallJetstream::VALID_STACKS) as $stackChoice) {
                foreach ([true, false] as $useTeams) {
                    $consoleWriter = $this->mock(ConsoleWriter::class, function (MockInterface $consoleWriter) use ($useTeams, $stackChoice) {
                        $consoleWriter->shouldReceive('note')->once()->globally()->ordered();
                        $consoleWriter->shouldReceive('choice')->once()->globally()->ordered()
                            ->with(Mockery::type('string'), array_keys(InstallJetstream::VALID_STACKS))
                            ->andReturn($stackChoice);
                        $consoleWriter->shouldReceive('confirm')->once()->globally()->ordered()->andReturn($useTeams);
                        $consoleWriter->shouldReceive('ok')->once()->globally()->ordered();
                    });
                    config(['lambo.store' => []]);

                    (new SetConfig(
                        $commandLineConfiguration,
                        $this->mock(SavedConfiguration::class),
                        $this->mock(ShellConfiguration::class),
                        $consoleWriter,
                        new FakeInput([
                            'jetstream' => $invalidStack,
                        ])
                    ))([
                        'jetstream' => false,
                    ]);

                    static::assertEquals(Str::lower($stackChoice) . ($useTeams ? ',teams' : ''), config('lambo.store.jetstream'));
                    static::assertFalse(config('lambo.store.breeze'));

                    if (in_array('--debug', $_SERVER['argv'], true)) {
                        $this->toSTDOUT(sprintf('[ Options ] %s', is_null($invalidStack) ? '--breeze' : "--breeze={$invalidStack}"));
                        $this->toSTDOUT(sprintf('[ Choice  ] %s%s', Str::lower($stackChoice), $useTeams ? ' with teams' : ''));
                        $this->toSTDOUT(sprintf('[ Config  ] lambo.store.jetstream => %s', config('lambo.store.jetstream') ? config('lambo.store.jetstream') : 'false'));
                        $this->toSTDOUT(sprintf('[ Config  ] lambo.store.breeze => %s', config('lambo.store.breeze') ? config('lambo.store.breeze') : 'false'));
                        $this->toSTDOUT('-------------');
                    }
                }
            }
        }
    }
}
