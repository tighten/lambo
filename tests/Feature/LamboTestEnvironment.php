<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;

trait LamboTestEnvironment
{
    protected function withValetTld($tld = 'test'): void
    {
        $valetConfig = config('home_dir') . '/.config/valet/config.json';

        File::shouldReceive('isFile')
            ->with($valetConfig)
            ->andReturnTrue();

        File::shouldReceive('get')
            ->with($valetConfig)
            ->andReturn(sprintf('{"tld": "%s"}', $tld));
    }
}
