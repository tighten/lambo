<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class LamboCommand extends Command
{
    public function handle()
    {
        app()->bind('console', function () {
            return $this;
        });

        app()->singleton('console-writer', function () {
            return new class($this->input, $this->output) {
                private $consoleWriter;
                private $noAnsi;
                private $verbosityLevel;

                private $ignoreVerbosity = false;

                public function __construct(InputInterface $input, OutputInterface $output)
                {
                    $this->consoleWriter = new SymfonyStyle($input, $output);
                    $this->noAnsi = ! $this->consoleWriter->isDecorated();

                    $this->verbosityLevel = $this->consoleWriter->getVerbosity();
                }

                public function block(string $prefix, string $message, string $style)
                {
                    if ($this->ignoreVerbosity || $this->verbosityLevel > SymfonyStyle::VERBOSITY_NORMAL) {
                        $this->consoleWriter->block($message, $prefix, $style, ' ', true, false);
                    }

                    $this->ignoreVerbosity = false;
                }

                public function section($sectionTitle)
                {
                    if ($this->ignoreVerbosity || $this->verbosityLevel > SymfonyStyle::VERBOSITY_NORMAL) {
                        $this->consoleWriter->text([
                            "<fg=yellow;bg=default>{$sectionTitle}</>",
                            '<fg=yellow;bg=default>' . str_repeat('#', strlen($sectionTitle)) . '</>',
                            ''
                        ]);
                    }

                    $this->ignoreVerbosity = false;
                }

                public function logStep($message)
                {
                    $this->consoleWriter->block($message, null, 'fg=yellow;bg=default', ' // ', false, false);
                    $this->ignoreVerbosity = false;
                }

                public function success($message, $prefix = 'PASS'): void
                {
                    $this->prefixedText($message, $prefix, 'fg=black;bg=green');
                }

                public function note($message, $prefix = 'NOTE'): void
                {
                    $this->prefixedText($message, $prefix, 'fg=black;bg=yellow');
                }

                public function warn($message, $prefix = 'WARN'): void
                {
                    $this->prefixedText("<fg=red;bg=default>{$message}</>", $prefix, 'fg=black;bg=red');
                }

                public function fail($message, $prefix = 'FAIL'): void
                {
                    $this->prefixedText($message, $prefix, 'fg=black;bg=red');
                }

                public function exception($message)
                {
                    $this->consoleWriter->block($message, null, 'fg=black;bg=red', ' ', true, false);

                    $this->ignoreVerbosity = false;
                }

                public function text($message)
                {
                    if ($this->ignoreVerbosity || $this->verbosityLevel > SymfonyStyle::VERBOSITY_NORMAL) {
                        $this->consoleWriter->text($message);
                    }

                    $this->ignoreVerbosity = false;
                }

                public function listing(array $items): void
                {
                    if ($this->ignoreVerbosity || $this->verbosityLevel > SymfonyStyle::VERBOSITY_NORMAL) {
                        $this->consoleWriter->newLine();
                        $text = collect($items)->map(function ($dependency) {
                            return '  - ' . $dependency;
                        })->toArray();
                        $this->consoleWriter->text($text);
                        $this->consoleWriter->newLine();
                    }

                    $this->ignoreVerbosity = false;
                }

                public function table(array $columnHeadings, array $rowData)
                {
                    if ($this->ignoreVerbosity || $this->verbosityLevel > SymfonyStyle::VERBOSITY_NORMAL) {
                        $this->consoleWriter->table($columnHeadings, $rowData);
                    }

                    $this->ignoreVerbosity = false;
                }

                private function prefixedText(string $message, string $prefix, string $prefixFormat='fg=default;bg=default'): void
                {
                    if ($this->ignoreVerbosity || $this->verbosityLevel > SymfonyStyle::VERBOSITY_NORMAL) {
                        $this->noAnsi
                            ? $this->consoleWriter->text("[{$prefix}]" . $message)
                            : $this->consoleWriter->text("<{$prefixFormat}> {$prefix} </> " . $message);
                    }

                    $this->ignoreVerbosity = false;
                }

                public function isDebug(): bool
                {
                    return $this->verbosityLevel >= SymfonyStyle::VERBOSITY_DEBUG;
                }

                public function isVerbose(): bool
                {
                    return $this->verbosityLevel > SymfonyStyle::VERBOSITY_NORMAL;
                }

                public function ignoreVerbosity()
                {
                    $this->ignoreVerbosity = true;

                    return $this;
                }

                public function __call($name, $arguments)
                {
                    return $this->consoleWriter->$name(...$arguments);
                }
            };
        });
    }
}
