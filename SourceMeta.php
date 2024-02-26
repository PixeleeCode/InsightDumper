<?php

namespace Pixelee\InsightDumper;

final class SourceMeta
{
    public static function getData(): string
    {
        $trace = debug_backtrace();
        $caller = $trace[1] ?? [];

        $metadataParts = [];

        if (isset($caller['file'])) {
            $metadataParts[] = 'File: ' . $caller['file'] ."\n";
        }

        if (isset($caller['line'])) {
            $metadataParts[] = 'Line: ' . $caller['line'] ."\n";
        }

        if (isset($caller['class'])) {
            $metadataParts[] = 'Class: ' . $caller['class'] ."\n";
        }

        if (isset($caller['function'])) {
            $metadataParts[] = 'Function: ' . $caller['function'] ."\n";
        }

        return implode('', $metadataParts);
    }
}
