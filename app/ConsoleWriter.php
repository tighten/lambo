<?php

namespace App;

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class ConsoleWriter extends OutputStyle
{
    private $onlyVerbose = false;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);
    }

    public function panel(string $prefix, string $message, string $style)
    {
        parent::block($message, $prefix, $style, ' ', true, false);
    }

    public function section($sectionTitle)
    {
        $this->text([
            "",
            "<fg=yellow;bg=default>{$sectionTitle}</>",
            '<fg=yellow;bg=default>' . str_repeat('#', strlen($sectionTitle)) . '</>',
        ]);
    }

    public function logStep($message)
    {
        parent::block($message, null, 'fg=yellow;bg=default', ' // ', false, false);
    }

    public function success($message, $label = 'PASS'): void
    {
        if ($this->getVerbosity() > SymfonyStyle::VERBOSITY_NORMAL && $this->onlyVerbose) {
            $this->labeledLine($label, $message, 'fg=black;bg=green');
        }
        $this->onlyVerbose = false;
    }

    public function ok($message): void
    {
        $this->success($message, ' OK ');
    }

    public function note($message, $label = 'NOTE'): void
    {
        if ($this->getVerbosity() > SymfonyStyle::VERBOSITY_NORMAL && $this->onlyVerbose) {
            $this->labeledLine($label, $message, 'fg=black;bg=yellow');
        }
        $this->onlyVerbose = false;
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
    }

    public function text($message)
    {
        parent::text($message);
    }

    public function listing(array $items): void
    {
        parent::newLine();
        $text = collect($items)->map(function ($dependency) {
            return '  - ' . $dependency;
        })->toArray();
        parent::text($text);
        parent::newLine();
    }

    public function table(array $columnHeadings, array $rowData)
    {
        parent::table($columnHeadings, $rowData);
    }

    public function labeledLine(string $label, string $message, string $labelFormat = 'fg=default;bg=default', int $indent = 0): void
    {
        $indentString = str_repeat(' ', $indent);
        $this->isDecorated()
            ? parent::text("{$indentString}<{$labelFormat}> {$label} </> {$message}")
            : parent::text("{$indentString}[{$label}] {$message}");
    }

    public function exec(string $command)
    {
        if ($this->getVerbosity() > SymfonyStyle::VERBOSITY_NORMAL && $this->onlyVerbose) {
            $this->labeledLine('EXEC', $command, 'bg=blue;fg=black');
        }
        $this->onlyVerbose = false;
    }

    public function consoleOutput(string $line, $type)
    {
        ($type === Process::ERR)
            ? $this->labeledLine('!', "<fg=yellow>{$line}</>", 'bg=yellow;fg=black', 3)
            : $this->labeledLine('✓', "{$line}", 'bg=blue;fg=black', 3);
    }

    public function verbose()
    {
        $this->onlyVerbose = true;
        return $this;
    }

    public function shouldWriteLine()
    {
        return $this->getVerbosity() > SymfonyStyle::VERBOSITY_NORMAL;
    }
}
