<?php

namespace Tests\Unit;

use App\Configuration\CommandLineConfiguration;
use App\Configuration\SavedConfiguration;
use App\Configuration\SetConfig;
use App\Configuration\ShellConfiguration;
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
            $this->mock(ShellConfiguration::class)
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
            $this->mock(ShellConfiguration::class)
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
            $this->mock(ShellConfiguration::class)
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
            $shellConfiguration
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
            $shellConfiguration
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
            $shellConfiguration
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
            $shellConfiguration
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
            $this->mock(ShellConfiguration::class)
        ))(['root_path' => getcwd()]);

        $this->assertEquals('/home/user/path/from/command/line', config('lambo.store.root_path'));

        $savedConfiguration = $this->mock(SavedConfiguration::class);
        $savedConfiguration->root_path = '~/path/from/saved/configuration';

        (new SetConfig(
            $this->mock(CommandLineConfiguration::class),
            $savedConfiguration,
            $this->mock(ShellConfiguration::class)
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
            $this->mock(ShellConfiguration::class)
        ))([
            'root_path' => getcwd(),
            'project_name' => null,
            'database_name' => null
        ]);

        $this->assertEquals('h_y_p_h_e_n_s', config('lambo.store.database_name'));
    }

    /* -------------------------------------------------------------------------------------------------------------- /*

     Options (lambo new myApplication OPTIONS):
       -b, --browser="BROWSER"      Open the site in the specified BROWSER. E.g. 'Google Chrome' or 'Safari' (macOS)
       -f, --frontend="FRONTEND"    Specify the FRONTEND framework to use. Must be one of bootstrap, react or vue
          --dbname=DBNAME           Specify the database name
          --dbuser=USERNAME         Specify the database user
          --dbpassword=PASSWORD     Specify the database password
          --create-db               Create a new MySQL database
       -a, --auth                   Scaffold the routes and views for basic Laravel auth
          --node                    Run 'npm install' after creating the project
       -x, --mix                    Run 'npm run dev' after creating the project
       -l, --link                   Create a Valet link to the project directory
       -s, --secure                 Generate and use an HTTPS cert with Valet
       -q, --quiet                  Use quiet mode to hide most messages from lambo
          --with-output             Show command line output from shell commands
       -d, --dev                    Install Laravel using the develop branch
          --full                    Shortcut of --create-db --link --secure --auth --node --mix
          --no-editor               Do not open the project in an editor

     /* ------------------------------------------------------------------------------------------------------------- */

    /** @test */
    function it_sets_the_project_url()
    {
        $this->withValetTld('test-domain');

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $commandLineConfiguration->project_name = 'foo';

        (new SetConfig(
            $commandLineConfiguration,
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class)
        ))([
            'tld' => null,
            'project_name' => null,
            'valet_secure' => false,
        ]);

        $this->assertEquals('http://foo.test-domain', config('lambo.store.project_url'));

        $commandLineConfiguration->valet_secure = true;

        (new SetConfig(
            $commandLineConfiguration,
            $this->mock(SavedConfiguration::class),
            $this->mock(ShellConfiguration::class)
        ))([
            'tld' => null,
            'project_name' => null,
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
            $this->mock(ShellConfiguration::class)
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
            $this->mock(ShellConfiguration::class)
        ))([
            'root_path' => getcwd(),
            'project_name' => null,
        ]);

        $this->assertEquals('/path/from/command/line/my-project', config('lambo.store.project_path'));
    }

    /** @test */
    function it_sets_the_create_database_configuration()
    {
        $this->withValetTld();
//        config(['home_dir' => '/home/user']);

        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->auth = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'auth' => false,
        ]);
        $this->assertTrue(config('lambo.store.auth'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->auth = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'auth' => false,
        ]);
        $this->assertTrue(config('lambo.store.auth'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->auth = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'auth' => false,
        ]);
        $this->assertFalse(config('lambo.store.auth'));


        /*
         * --full          = the value of the command line flag --full.
         *
         * --create-db     = the value of the command line flag --create-db.
         *
         * CREATE_DATABASE = the configuration value of the same name that is
         *                   stored in ~/.lambo/config.
         *
         * create_database = the value that should be returned from
         *                   config('lambo.store.create_database')
         *                   after SetConfig has merged all configurations.
         */
        /*collect([
            [
                '--full' => false,
                '--create-db' => false,
                'CREATE_DATABASE' => false,
                'create_database' => false,
            ],
            [
                '--full' => false,
                '--create-db' => false,
                'CREATE_DATABASE' => true,
                'create_database' => true,
            ],
            [
                '--full' => false,
                '--create-db' => true,
                'CREATE_DATABASE' => false,
                'create_database' => true,
            ],
            [
                '--full' => false,
                '--create-db' => true,
                'CREATE_DATABASE' => true,
                'create_database' => true,
            ],
            [
                '--full' => true,
                '--create-db' => false,
                'CREATE_DATABASE' => false,
                'create_database' => true,
            ],
            [
                '--full' => true,
                '--create-db' => false,
                'CREATE_DATABASE' => true,
                'create_database' => true,
            ],
            [
                '--full' => true,
                '--create-db' => true,
                'CREATE_DATABASE' => false,
                'create_database' => true,
            ],
            [
                '--full' => true,
                '--create-db' => true,
                'CREATE_DATABASE' => true,
                'create_database' => true,
            ],
        ])->each(function($options) {
            (new SetConfig(
                $this->mock(CommandLineConfiguration::class, function($commandLineConfiguration) use ($options){
                    $commandLineConfiguration->full = $options['--full'];
                    $commandLineConfiguration->create_database = $options['--create-db'];
                }),
                $this->mock(SavedConfiguration::class, function($savedConfiguration) use ($options){
                    $savedConfiguration->create_database = $options['CREATE_DATABASE'];
                }),
                $this->mock(ShellConfiguration::class)
            ))([
                'full' => false,
                'create_database' => false,
            ]);

            $this->assertEquals($options['create_database'], config('lambo.store.create_database'));
        });*/
    }

    /** @test */
    function it_sets_the_node_configuration()
    {
        $this->withValetTld('test-domain');
        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->mix = false;
        $commandLineConfiguration->node = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'node' => false,
            'mix' => false,
        ]);
        $this->assertTrue(config('lambo.store.node'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->mix = true;
        $commandLineConfiguration->node = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'node' => false,
            'mix' => false,
        ]);
        $this->assertTrue(config('lambo.store.node'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->mix = false;
        $commandLineConfiguration->node = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'node' => false,
            'mix' => false,
        ]);
        $this->assertTrue(config('lambo.store.node'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->mix = false;
        $commandLineConfiguration->node = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'node' => false,
            'mix' => false,
        ]);
        $this->assertFalse(config('lambo.store.node'));
    }

    /** @test */
    function it_sets_the_mix_configuration()
    {

        $this->withValetTld('test-domain');
        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->mix = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'mix' => false,
        ]);
        $this->assertTrue(config('lambo.store.mix'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->mix = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'mix' => false,
        ]);
        $this->assertTrue(config('lambo.store.mix'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->mix = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'mix' => false,
        ]);
        $this->assertFalse(config('lambo.store.mix'));

    }

    /** @test */
    function it_sets_the_auth_configuration()
    {
        $this->withValetTld('test-domain');
        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->auth = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'auth' => false,
        ]);
        $this->assertTrue(config('lambo.store.auth'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->auth = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'auth' => false,
        ]);
        $this->assertTrue(config('lambo.store.auth'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->auth = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'auth' => false,
        ]);
        $this->assertFalse(config('lambo.store.auth'));
    }

    /** @test */
    function it_sets_the_valet_link_configuration()
    {
        $this->withValetTld('test-domain');
        $commandLineConfiguration = $this->mock(CommandLineConfiguration::class);
        $savedConfiguration = $this->mock(SavedConfiguration::class);

        $commandLineConfiguration->full = true;
        $commandLineConfiguration->valet_link = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'valet_link' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_link'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_link = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'valet_link' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_link'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_link = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
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
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => true,
            'valet_secure' => false,
        ]);
        $this->assertTrue(config('lambo.store.valet_secure'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_secure = true;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'valet_secure' => true,
        ]);
        $this->assertTrue(config('lambo.store.valet_secure'));

        $commandLineConfiguration->full = false;
        $commandLineConfiguration->valet_secure = false;
        (new SetConfig($commandLineConfiguration, $savedConfiguration, $this->mock(ShellConfiguration::class)))([
            'tld' => null,
            'full' => false,
            'valet_secure' => false,
        ]);
        $this->assertFalse(config('lambo.store.valet_secure'));
    }
}
