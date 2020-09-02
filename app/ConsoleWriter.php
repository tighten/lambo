<?php

namespace App;

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class ConsoleWriter extends OutputStyle
{
    private $ignoreVerbosity = false;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);
    }

    public function foo(string $prefix, string $message, string $style)
    {
        if ($this->ignoreVerbosity || $this->shouldWriteLine()) {
            parent::block($message, $prefix, $style, ' ', true, false);
        }

        $this->ignoreVerbosity = false;
    }

    public function section($sectionTitle)
    {
        if ($this->ignoreVerbosity || $this->shouldWriteLine()) {
            parent::text([
                "<fg=yellow;bg=default>{$sectionTitle}</>",
                '<fg=yellow;bg=default>' . str_repeat('#', strlen($sectionTitle)) . '</>',
                ''
            ]);
        }

        $this->ignoreVerbosity = false;
    }

    public function logStep($message)
    {
        parent::block($message, null, 'fg=yellow;bg=default', ' // ', false, false);
        $this->ignoreVerbosity = false;
    }

    public function success($message, $label = 'PASS'): void
    {
        $this->labeledLine($label, $message, 'fg=black;bg=green');
    }

    public function note($message, $label = 'NOTE'): void
    {
        $this->labeledLine($label, $message, 'fg=black;bg=yellow');
    }

    public function warn($message, $label = 'WARN'): void
    {
        $this->labeledLine($label, "<fg=red;bg=default>{$message}</>", 'fg=black;bg=red');
    }

    public function fail($message, $label = 'FAIL'): void
    {
        $this->labeledLine($label, $message, 'fg=black;bg=red');
    }

    public function exception($message)
    {
        parent::block($message, null, 'fg=black;bg=red', ' ', true, false);

        $this->ignoreVerbosity = false;
    }

    public function text($message)
    {
        if ($this->ignoreVerbosity || $this->shouldWriteLine()) {
            parent::text($message);
        }

        $this->ignoreVerbosity = false;
    }

    public function listing(array $items): void
    {
        if ($this->ignoreVerbosity || $this->shouldWriteLine()) {
            parent::newLine();
            $text = collect($items)->map(function ($dependency) {
                return '  - ' . $dependency;
            })->toArray();
            parent::text($text);
            parent::newLine();
        }

        $this->ignoreVerbosity = false;
    }

    public function table(array $columnHeadings, array $rowData)
    {
        if ($this->ignoreVerbosity || $this->shouldWriteLine()) {
            parent::table($columnHeadings, $rowData);
        }

        $this->ignoreVerbosity = false;
    }

    public function ignoreVerbosity()
    {
        $this->ignoreVerbosity = true;

        return $this;
    }

    public function labeledLine(string $label, string $message, string $labelFormat = 'fg=default;bg=default', int $indent = 0): void
    {
        if ($this->ignoreVerbosity || $this->shouldWriteLine()) {
            $indentString = str_repeat(' ', $indent);
            $this->isDecorated()
                ? parent::text("{$indentString}<{$labelFormat}> {$label} </> {$message}")
                : parent::text("{$indentString}[{$label}] {$message}");
        }

        $this->ignoreVerbosity = false;
    }

    public function stdERR($line)
    {
        $this->labeledLine('!', "<fg=yellow>{$line}</>", 'bg=yellow;fg=black', 3);
    }

    public function stdOUT($line)
    {
        $this->labeledLine('✓', "{$line}", 'bg=blue;fg=black', 3);
    }

    public function consoleOutput(string $line, $type)
    {
        ($type === Process::ERR)
            ? $this->stdERR($line)
            : $this->stdOUT($line);
    }

    private function shouldWriteLine()
    {

        return $this->getVerbosity() > SymfonyStyle::VERBOSITY_NORMAL;
    }
}
