<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

trait LamboTestEnvironment
{

    /*protected function withoutEnvironmentVariable(array $keys): void
    {
        Arr::forget($_SERVER, $keys);
    }*/

    protected function withValetTld($tld = 'test'): void
    {
        $valetConfig = Config::get('home_dir') . '/.config/valet/config.json';

        File::shouldReceive('isFile')
            ->with($valetConfig)
            ->andReturnTrue();

        File::shouldReceive('get')
            ->with($valetConfig)
            ->andReturn(sprintf('{"tld": "%s"}', $tld));
    }

    /*protected function withoutCommandLineOptions(): void
    {
        $this->withCommandLineOptions([]);
    }*/

    /*protected function withEnvironmentVariable(array $environmentVariables)
    {
        foreach ($environmentVariables as $key => $value) {
            Arr::set($_SERVER, $key, $value);
        }
    }*/

    /*protected function withCommandLineOptions(array $commandLineOptions): void
    {
        $this->swap('console', $this->mock(Command::class, function ($mock) use ($commandLineOptions) {
            $mock->shouldReceive('options')
                ->andReturn($commandLineOptions);
        }));
    }*/

    /*protected function withArgument(string $key, string $value): void
    {
        $this->swap('console', $this->mock(Command::class, function ($mock) use ($key, $value) {
            $mock->shouldReceive('argument')
                ->with($key)
                ->andReturn($value);
        }));
    }*/
}
