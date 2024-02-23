<?php

use Pixelee\InsightDumper\InsightDumper;

if (!function_exists('pp')) {
    function pp(mixed ...$vars): mixed
    {
        $dump = null;
        if (!$vars) {
            $dump = InsightDumper::dump('🫣');
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            $dump = InsightDumper::dump($vars[0]);
        } else {
            foreach ($vars as $value) {
                $dump .= InsightDumper::dump($value);
            }
        }

        if (1 < count($vars)) {
            return $vars;
        }

        return $dump;
    }
}
