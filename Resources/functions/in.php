<?php

use Pixelee\InsightDumper\InsightDumper;
use Pixelee\InsightDumper\Response;

if (!function_exists('in')) {
    function in(mixed ...$vars): null|string|array
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

        /*if (1 < count($vars)) {
            return $vars;
        }*/

        $response = new Response($dump);
        $response->send();

        return null;
    }
}
