<?php

namespace App;

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class ConsoleWriter extends OutputStyle
{
    private $onlyVerbose = false;

    public function panel(string $prefix, string $message, string $style)
    {
        if ($this->shouldWriteLine(false)) {
            parent::block($message, $prefix, $style, ' ', true, false);
        }
    }

    public function sectionTitle($sectionTitle)
    {
        if ($this->shouldWriteLine(true)) {
            $this->newLine();
            $this->text([
                "<fg=yellow;bg=default>{$sectionTitle}</>",
                '<fg=yellow;bg=default>' . str_repeat('#', strlen($sectionTitle)) . '</>',
            ]);
        }
    }

    public function logStep($message)
    {
        if ($this->shouldWriteLine(true)) {
            parent::block($message, null, 'fg=yellow;bg=default', ' // ', false, false);
        }
    }

    public function exec(string $command)
    {
        if ($this->shouldWriteLine(true)) {
            $this->labeledLine('EXEC', $command, 'bg=blue;fg=black');
        }
    }

    public function success($message, $label = 'PASS'): void
    {
        if ($this->shouldWriteLine(true)) {
            $this->labeledLine($label, $message, 'fg=black;bg=green');
        }
    }

    public function ok($message): void
    {
        if ($this->shouldWriteLine(false)) {
            $this->success($message, ' OK ');
        }
    }

    public function note($message, $label = 'NOTE'): void
    {
        if ($this->shouldWriteLine(false)) {
            $this->labeledLine($label, $message, 'fg=black;bg=yellow');
        }
    }

    public function warn($message, $label = 'WARN'): void
    {
        if ($this->shouldWriteLine(true)) {
            $this->labeledLine($label, "<fg=red;bg=default>{$message}</>", 'fg=black;bg=red');
        }
    }

    public function exception($message)
    {
        if ($this->shouldWriteLine(true)) {
            parent::block($message, null, 'fg=black;bg=red', ' ', true, false);
        }
    }

    public function text($message)
    {
        parent::text($message);
    }

    public function listing(array $items): void
    {
        if ($this->shouldWriteLine(false)) {
            parent::newLine();
            $text = collect($items)->map(function ($dependency) {
                return '  - ' . $dependency;
            })->toArray();
            parent::text($text);
            parent::newLine();
        }
    }

    public function table(array $columnHeadings, array $rowData)
    {
        if ($this->shouldWriteLine(false)) {
            parent::table($columnHeadings, $rowData);
        }
    }

    public function consoleOutput(string $line, $type)
    {
        if ($this->shouldWriteLine(true)) {
            ($type === Process::ERR)
                ? $this->labeledLine('!', "<fg=yellow>{$line}</>", 'bg=yellow;fg=black', 3)
                : $this->labeledLine('✓', "{$line}", 'bg=blue;fg=black', 3);
        }
    }

    public function verbose()
    {
        $this->onlyVerbose = true;
        return $this;
    }

    private function labeledLine(string $label, string $message, string $labelFormat = 'fg=default;bg=default', int $indent = 0): void
    {
        $indentString = str_repeat(' ', $indent);
        $this->isDecorated()
            ? parent::text("{$indentString}<{$labelFormat}> {$label} </> {$message}")
            : parent::text("{$indentString}[{$label}] {$message}");
    }

    private function shouldWriteLine($ignoreVerbosity = true)
    {
        if ($this->isDebug()) {
            return true;
        }

        if ($ignoreVerbosity) {
            $this->onlyVerbose = false;
            return true;
        }

        if (($this->getVerbosity() > SymfonyStyle::VERBOSITY_NORMAL) && $this->onlyVerbose) {
            $this->onlyVerbose = false;
            return true;
        }

        return false;
    }
}
