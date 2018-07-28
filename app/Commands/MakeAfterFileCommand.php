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
    protected $signature = 'make:after';

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
        $homeFolder = $_SERVER['HOME'];

        $filePath = $homeFolder . '/.lambo/after.php';

        if (File::exists($filePath)) {
            $this->error("Config file already exists at [{$filePath}].");
            exit(1);
        }

        File::put($filePath, File::get(base_path('/stubs/after.php')));

        if (File::exists($filePath)){
            $this->info("File successfully created at [{$filePath}]");
        } else {
            $this->error("Error creating file [{$filePath}]");
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
