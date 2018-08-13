<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MakeConfigFileCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make:config {--force : Force overwrite existing "after" file}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Creates a config file so you don't have to pass the parameters every time you use Lambo";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $homeFolder = $_SERVER['HOME'];

        $filePath = $homeFolder . '/.lambo/config.php';

        if (File::exists($filePath) && !$this->option('force')) {
            $this->error("Config file already exists at [{$filePath}].");
            exit(1);
        }

        File::put($filePath, File::get(base_path('/stubs/config.stub')));

        if (File::exists($filePath)) {
            $this->info("File successfully created at [{$filePath}]");
        } else {
            $this->error("Error creating file [{$filePath}]");
        }
    }
}
