<?php

use Pixelee\InsightDumper\InsightDumper;
use Pixelee\InsightDumper\Response;

if (!function_exists('in')) {

    define('INSIGHT_DUMPER_START', microtime(true));

    /**
     * A convenient wrapper function for dumping variables.
     * It supports multiple variables and uses the InsightDumper class for formatting.
     * It can output a single variable or iterate through multiple variables if more than one is provided.
     * Also handles sending the output as an HTTP response via the Response class.
     *
     * @param mixed ...$vars Variables to be dumped. Accepts multiple inputs.
     * @return string|array|null Always returns null to indicate the output is sent directly to the HTTP response.
     * @throws ReflectionException If a reflection error occurs during the dumping process.
     */
    function in(mixed ...$vars): null|string|array
    {
        $dump = null;
        $backtrace = debug_backtrace();
        $callerInfo = $backtrace[0];

        $file = $callerInfo['file'] ?? 'N/A';
        $line = $callerInfo['line'] ?? 'N/A';

        $executionTime = microtime(true) - INSIGHT_DUMPER_START;

        if (!$vars) {
            $dump = InsightDumper::dump('🫣', $file, $line, $executionTime);
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            $dump = InsightDumper::dump($vars[0], $file, $line, $executionTime);
        } else {
            foreach ($vars as $value) {
                $dump .= InsightDumper::dump($value, $file, $line, $executionTime);
            }
        }

        $response = new Response($dump);
        $response->send();

        return null;
    }
}
