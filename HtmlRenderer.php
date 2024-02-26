<?php

namespace Pixelee\InsightDumper;

/**
 * Provides utilities for rendering PHP data types in a human-readable format.
 */
final class HtmlRenderer
{
    /**
     * Generates HTML markup for different data types.
     *
     * @param string $class The CSS class to apply.
     * @param string $content The content to display.
     * @param string $tag The HTML tag to use.
     * @return string The generated HTML string.
     */
    public static function wrap(string $class, string $content, string $tag = 'span'): string
    {
        return sprintf(
            '<%1$s class="%2$s">%3$s</%1$s>',
            $tag,
            $class,
            $content
        );
    }
}
