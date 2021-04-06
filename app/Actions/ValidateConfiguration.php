<?php

namespace App\Actions;

use App\Commands\Debug;
use App\ConsoleWriter;
use App\Shell;

class ValidateConfiguration
{
    use Debug;

    protected $consoleWriter;
    protected $shell;
    protected $finder;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->consoleWriter = $consoleWriter;
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Validating configuration');

        app(ValidateFrontendConfiguration::class)();
        $this->consoleWriter->newLine();
        app(ValidateGithubConfiguration::class)();

        $this->consoleWriter->success('Configuration is valid.');

        if ($this->consoleWriter->isDebug()) {
            $this->debugReport();
        }
    }

    protected function debugReport(): void
    {
        $this->consoleWriter->panel('Debug', 'Start', 'fg=black;bg=white');

        $this->consoleWriter->text([
            'Configuration may have changed after validation',
            'Configuration is now as follows:',
        ]);
        $this->configToTable();

        $this->consoleWriter->panel('Debug', 'End', 'fg=black;bg=white');
    }
}
