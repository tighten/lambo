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
     * @var string
     */
    protected $filePath;

    /**
     * @var Collection
     */
    protected $replaces;

    /**
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
     */
    public function __invoke()
    {
        $this->filePath = str_finish(config('lambo-store.project_path'), '/') . '.env';

        $this->hydrateReplaces();

        try {
            $this->hydrateFileLines();
        } catch (LogicException $exception) {
            $this->console->error('Unable to open project root .env file!');
            return;
        }

        $this->performReplaces();

        $this->save();
    }

    /**
     * Hydrate replaces
     *
     */
    protected function hydrateReplaces(): void
    {
        $replaces = [
            'APP_NAME'      => config('lambo-store.project_name'),
            'APP_URL'       => config('lambo-store.project_url'),
        ];

        if (config('lambo.database') === 'mysql') {
            $replaces = array_merge($replaces, [
                'DB_CONNECTION' => config('lambo.database'),
                'DB_DATABASE'   => config('lambo-store.db_name'),
                'DB_HOST'       => config('lambo.db_host'),
                'DB_PORT'       => config('lambo.db_port'),
                'DB_USERNAME'   => config('lambo.db_username'),
                'DB_PASSWORD'   => config('lambo.db_password'),
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
     * Hydrates the file lines collection
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
     * Perform the replaces
     *
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
            $replace = $this->replaces->first(function ($item, $key) use ($envKey) {
                return $key === $envKey;
            });

            // If found, assign it, else return same
            if ($replace !== null) {
                return "{$envKey}={$replace}\n";
            }

            return $item;
        });

    }

    /**
     * Save the changes
     *
     * @throws LogicException
     */
    protected function save(): void
    {
        $fileAsString = $this->fileLines->implode('');

        File::put($this->filePath, $fileAsString);
    }
}
