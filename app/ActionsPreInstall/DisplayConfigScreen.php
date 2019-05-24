<?php

namespace App\ActionsPreInstall;

use App\Facades\OptionManager;
use App\Support\BaseAction;

class DisplayConfigScreen extends BaseAction
{
    /**
     * Displays the Initial screen, showing current config, and ability to customise.
     *
     * @param string|null $message
     * @param string|null $level
     * @return void
     */
    public function __invoke(?string $message = null, ?string $level = 'info'): void
    {
        $emptySpace = '';
        foreach (range(1, 15) as $i) {
            $emptySpace .= PHP_EOL;
        }
        $this->console->info($emptySpace);

        $this->console->action(DisplayLamboLogo::class);

        $this->displayCurrentConfig();

        if ($message !== null) {
            $this->console->info('');
            switch ($level) {
                case 'error':
                    $this->console->error($message);
                    break;
                case 'alert':
                    $this->console->alert($message);
                    break;
                default:
                    $this->console->info($message);
            }
        }

        $this->console->action(PromptForCustomization::class);
    }

    /**
     * Displays a table with the current configuration values.
     *
     * @return void
     */
    private function displayCurrentConfig(): void
    {
        $options = OptionManager::optionValuesForDisplay();

        $this->console->table(['Option', 'Description', 'Value'], $options);
    }
}
