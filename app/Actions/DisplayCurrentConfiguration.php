<?php

namespace App\Actions;

use function is_bool;
use function is_string;
use App\Support\BaseAction;

class DisplayCurrentConfiguration extends BaseAction
{
    public function __invoke()
    {
        $rows = config('lambo', []);

        $rows = collect($rows)->filter(function ($item, $key) {
            return $key !== 'after';
        })->map(function ($item, $key) {
            if (is_bool($item)) {
                $item = $item ? 'true' : 'false';
            }

            if (is_string($item) && $item === '') {
                $item = '(empty)';
            }

            return [
                'Configuration' => $key,
                'Value' => $item,
            ];
        })->all();

        $this->console->table(['Configuration', 'Value'], $rows);
    }
}
