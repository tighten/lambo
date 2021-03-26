<?php

namespace Tests\Feature;

use App\Actions\EditConfigFile;
use App\LamboException;
use Illuminate\Support\Facades\File;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class EditConfigFileTest extends TestCase
{
    private $fileName;
    private $configDirectory;
    private $configFilePath;
    private $fileTemplate;
    public function setUp(): void
    {
        parent::setUp();

        $this->fileName = 'my-config-file-name';
        $homeDirectory = '/my/home/directory';
        $this->configDirectory = "{$homeDirectory}/.lambo";
        $this->configFilePath = "{$this->configDirectory}/{$this->fileName}";

        config(['home_dir' => $homeDirectory]);
        config(['lambo.store.editor' => 'vim']);

        $this->fileTemplate = 'my-config-file-template';
    }

    /** @test */
    function it_creates_the_config_directory_and_file_then_opens_the_file_for_editing()
    {
        $when = $and = $then = $this;

        $when->the_config_directory_does_not_exist();
        $and->the_config_directory_is_created();

        $and->the_config_file_does_not_exist();
        $and->the_config_file_is_created();

        $then->the_config_file_is_opened_for_editing();

        app(EditConfigFile::class)($this->fileName);
    }

    /** @test */
    function it_creates_a_config_file_then_opens_the_file_for_editing()
    {
        $this->configDirectoryExists();
        $this->configFileExists(false);
        $this->successfullyCreateConfigFile();
        $this->successfullyOpenInEditor();

        app(EditConfigFile::class)($this->fileName);
    }

    /** @test */
    function it_opens_a_config_file_for_editing()
    {
        $this->configDirectoryExists();
        $this->configFileExists();
        $this->successfullyOpenInEditor();

        app(EditConfigFile::class)($this->fileName);
    }

    /** @test */
    function it_throws_an_exception_if_the_configured_editor_fails_to_open()
    {
        $this->configDirectoryExists();
        $this->configFileExists();
        $this->successfullyOpenInEditor(false);

        $this->expectException(LamboException::class);

        app(EditConfigFile::class)($this->fileName);
    }

    /** @test */
    function failing_to_create_the_configuration_directory_throws_an_exception()
    {
        $this->configDirectoryExists(false);
        $this->successfullyCreateConfigDirectory(false);

        $this->expectException(LamboException::class);

        app(EditConfigFile::class)($this->fileName);
    }

    /** @test */
    public function failing_to_create_the_configuration_file_throws_an_exception()
    {
        $this->configDirectoryExists();
        $this->configFileExists(false);
        $this->successfullyCreateConfigFile(false);

        $this->expectException(LamboException::class);

        app(EditConfigFile::class)($this->fileName);
    }

    private function configDirectoryExists(bool $exists = true): void
    {
        File::shouldReceive('isDirectory')
            ->with($this->configDirectory)
            ->once()
            ->andReturn($exists)
            ->globally()
            ->ordered();
    }

    private function successfullyCreateConfigDirectory(bool $success = true): void
    {
        File::shouldReceive('makeDirectory')
            ->with($this->configDirectory)
            ->once()
            ->andReturn($success)
            ->globally()
            ->ordered();
    }

    private function configFileExists(bool $success = true): void
    {
        File::shouldReceive('isFile')
            ->with($this->configFilePath)
            ->once()
            ->andReturn($success)
            ->globally()
            ->ordered();
    }

    private function successfullyCreateConfigFile(bool $success = true): void
    {
        File::shouldReceive('get')->with(base_path("stubs/{$this->fileName }"))
            ->once()
            ->andReturn($this->fileTemplate)
            ->globally()
            ->ordered();

        File::shouldReceive('put')
            ->with($this->configFilePath, $this->fileTemplate)
            ->once()
            ->andReturn($success)
            ->globally()
            ->ordered();
    }

    private function successfullyOpenInEditor(bool $success = true)
    {
        $this->shell->shouldReceive('withTTY')
            ->once()
            ->globally()
            ->andReturnSelf()
            ->ordered();

        $command = "vim {$this->fileName}";
        $expectation = $this->shell->shouldReceive('execIn')
            ->with($this->configDirectory, $command)
            ->once();

        if ($success) {
            $expectation->andReturn(FakeProcess::success());
        } else {
            $expectation->andReturn(FakeProcess::fail($command));
        }

        return $expectation->globally()->ordered();
    }

    private function the_config_directory_does_not_exist()
    {
        File::shouldReceive('isDirectory')
            ->with($this->configDirectory)
            ->once()
            ->andReturn(false)
            ->globally()
            ->ordered();
    }

    private function the_config_directory_is_created()
    {
        File::shouldReceive('makeDirectory')
            ->with($this->configDirectory)
            ->once()
            ->andReturn(true)
            ->globally()
            ->ordered();
    }

    private function the_config_file_does_not_exist()
    {
        File::shouldReceive('isFile')
            ->with($this->configFilePath)
            ->once()
            ->andReturn(false)
            ->globally()
            ->ordered();
    }

    private function the_config_file_is_created()
    {
        File::shouldReceive('get')->with(base_path("stubs/{$this->fileName }"))
            ->once()
            ->andReturn($this->fileTemplate)
            ->globally()
            ->ordered();

        File::shouldReceive('put')
            ->with($this->configFilePath, $this->fileTemplate)
            ->once()
            ->andReturn(true)
            ->globally()
            ->ordered();
    }

    private function the_config_file_is_opened_for_editing()
    {
        $this->shell->shouldReceive('withTTY')
            ->once()
            ->globally()
            ->andReturnSelf()
            ->ordered();

        $this->shell->shouldReceive('execIn')
            ->with($this->configDirectory, "vim {$this->fileName}")
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();
    }
}
