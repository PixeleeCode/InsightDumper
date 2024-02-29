<?php

namespace Pixelee\InsightDumper;

use Pixelee\InsightDumper\Render\RenderResource;

/**
 * The InsightDumper class is designed to format and stylize the display of PHP data.
 * It primarily uses a static method to generate the display.
 */
final class InsightDumper
{
    /**
     * Generates the HTML needed to display the passed data.
     * It incorporates CSS styles for better readability and can be extended to include JS scripts for interactive features.
     *
     * @param mixed $vars The data to display. Can be of any type supported by Render::render.
     * @return string The resulting HTML code for the data display.
     * @throws \ReflectionException If an error occurs while using reflection in Render::render.
     */
    public static function dump(mixed $vars, string $file = RenderResource::NOT_APPLICABLE, int $line = 0): string
    {
        $output = '<link rel="stylesheet" type="text/css" href="Resources/css/insight-dumper.css">';
        $output .= '<script src="Resources/js/insight-dumper.js"></script>';

        $output .= '<div class="insight-dump-wrapper">';
        $output .= "<p>$file at line $line</p>";
        $output .= Render::render($vars);
        $output .= '</div>';

        return $output;
    }
}
