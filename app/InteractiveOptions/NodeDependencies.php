<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class NodeDependencies extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'node';

    /**
     * Performs interactively.
     *
     * @param $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $menuTitle = 'Install Node dependencies? Which package manager?';

        $options = [
            'true'  => 'Yes. (First try yarn, then npm. If available)',
            'yarn'  => 'Yarn',
            'npm'   => 'NPM',
            'false' => 'No',
        ];

        $this->value = $console
            ->menu($menuTitle, $options)
            ->open();

        return $this;
    }
}
