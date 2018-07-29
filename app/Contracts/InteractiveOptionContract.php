<?php

namespace App\Contracts;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

interface InteractiveOptionContract
{
    /**
     * Performs the option interactively, using the given console command. Console menu
     * https://github.com/nunomaduro/laravel-console-menu is available and of course,
     * all the great Laravel Artisan Console methods. No option made? Return null.
     *
     * @param $console
     * @return BaseInteractiveOption|null
     */
    public function perform(NewCommand $console): ?BaseInteractiveOption;

    /**
     * Option key
     *
     * @return string
     */
    public function key(): string;

    /**
     * Option value
     *
     * @return string
     */
    public function value(): string;
}
