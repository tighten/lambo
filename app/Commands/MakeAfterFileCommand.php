<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MakeAfterFileCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make:after {--force : Force overwrite existing "after" file}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates an "after" file so you can run additional commands after Lambo finishes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationFolder = $_SERVER['HOME'] . '/.lambo';

        $filePath = $destinationFolder . '/after.php';

        if (File::exists($filePath) && !$this->option('force')) {
            $this->error("Config file already exists at [{$filePath}].");
            exit(1);
        }

        if (! File::isDirectory($destinationFolder)) {
            File::makeDirectory($destinationFolder);
        }

        File::put($filePath, File::get(base_path('/stubs/after.stub')));

        if (File::exists($filePath)) {
            $this->info("File successfully created at [{$filePath}]");
        } else {
            $this->error("Error creating file [{$filePath}]");
        }
    }
}
