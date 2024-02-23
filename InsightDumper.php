<?php

namespace Pixelee\InsightDumper;

require_once __DIR__ . '/Resources/functions/in.php';

final class InsightDumper
{
    public static function dump(mixed $vars): string
    {
        echo '<link rel="stylesheet" type="text/css" href="Resources/css/insight-dumper.css">';

        $output = '<div class="insight-dump-wrapper">';
        $output .= Render::render($vars);
        $output .= '</div>';

        return $output;
    }
}
