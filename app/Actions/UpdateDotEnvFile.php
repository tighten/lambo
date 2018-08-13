<?php

namespace App\Actions;

use function count;
use LogicException;
use App\Support\BaseAction;
use App\Commands\NewCommand;
use App\Support\ShellCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class UpdateDotEnvFile extends BaseAction
{
    /**
     * The .env file path.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The replaces to be made, by key-value.
     *
     * @var Collection
     */
    protected $replaces;

    /**
     * The .env file lines.
     *
     * @var Collection
     */
    protected $fileLines;

    /**
     * UpdateDotEnvFile constructor.
     *
     * @param NewCommand $console
     * @param ShellCommand $shell
     */
    public function __construct(NewCommand $console, ShellCommand $shell)
    {
        parent::__construct($console, $shell);
        $this->replaces = collect();
        $this->fileLines = collect();
    }

    /**
     * Run the substitutions.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $filePath = str_finish(config('lambo-store.project_path'), '/') . '.env';

        if (File::exists($filePath)) {
            $this->filePath = $filePath;
        } else {
            $this->console->error("Couldn't find .env file in: [{$filePath}]");
            return;
        }

        $this->hydrateReplaces();

        try {
            $this->hydrateFileLines();
        } catch (LogicException $exception) {
            $this->console->error('Unable to open project root .env file!');
            return;
        }

        $this->performReplaces();

        if (config('lambo.database') === 'sqlite') {
            $this->commentKeysForSqlite();
        }

        $this->save();
    }

    /**
     * Hydrate replaces.
     *
     * @return void
     */
    protected function hydrateReplaces(): void
    {
        $replaces = [
            'APP_NAME' => config('lambo-store.project_name'),
            'APP_URL' => config('lambo-store.project_url'),
        ];

        if (config('lambo.database') === 'mysql') {
            $replaces = array_merge($replaces, [
                'DB_CONNECTION' => config('lambo.database'),
                'DB_DATABASE' => config('lambo-store.db_name'),
                'DB_HOST' => config('lambo.db_host'),
                'DB_PORT' => config('lambo.db_port'),
                'DB_USERNAME' => config('lambo.db_username'),
                'DB_PASSWORD' => config('lambo.db_password'),
            ]);
        }

        if (config('lambo.database') === 'sqlite') {
            $replaces = array_merge($replaces, [
                'DB_CONNECTION' => config('lambo.database'),
            ]);
        }

        $this->replaces = collect($replaces);
    }

    /**
     * Hydrates the file lines collection.
     *
     * @throws LogicException
     */
    protected function hydrateFileLines(): void
    {
        if ($file = fopen($this->filePath, "r")) {
            while (!feof($file)) {
                $this->fileLines->push(fgets($file));
            }
            fclose($file);
        } else {
            throw new LogicException('Unable to open project root .env file!');
        }
    }

    /**
     * Perform the replaces.
     *
     * @return void
     */
    protected function performReplaces(): void
    {
        $this->fileLines->transform(function ($item, $key) {
            $parts = explode('=', $item, 2);

            // Line doesn't contain an equal sign (=), return same
            if (count($parts) < 2) {
                return $item;
            }

            [$envKey, $envVal] = $parts;

            // Find a replace for the key
            $replace = $this->replaces->get($envKey);

            // If found, assign it, else return same
            if ($replace !== null) {
                return "{$envKey}={$replace}\n";
            }

            return $item;
        });
    }

    /**
     * Comments keys for Sqlite on .env file.
     *
     * @return void
     */
    protected function commentKeysForSqlite(): void
    {
        $this->fileLines
            ->transform(function ($item, $key) {
                if (str_contains($item, ['DB_DATABASE', 'DB_HOST', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD'])) {
                    return "#{$item}";
                }
                return $item;
            });
    }

    /**
     * Save the changes.
     *
     * @return void
     */
    protected function save(): void
    {
        $fileAsString = $this->fileLines->implode('');

        File::put($this->filePath, $fileAsString);
    }
}
