<?php

namespace Pixelee\InsightDumper;

/**
 * Provides utilities for rendering PHP data types in a human-readable format.
 */
final class HtmlRenderer
{
    public const OPEN_TAG = 1;
    public const CLOSE_TAG = 2;
    public const FULL_TAG = 3;

    /**
     * Generates HTML markup for different data types.
     *
     * @param string $class The CSS class to apply.
     * @param string $content The content to display.
     * @param string $tag The HTML tag to use.
     * @param int $tagType The type of tag to generate (open, close, or full).
     * @return string The generated HTML string.
     */
    public static function wrap(string $class, string $content = '', string $tag = 'span', int $tagType = self::FULL_TAG): string
    {
        $tag = empty($tag) ? 'span' : $tag;

        return match ($tagType) {
            self::OPEN_TAG => sprintf('<%s class="%s">%s', $tag, $class, $content),
            self::CLOSE_TAG => sprintf('%s</%s>', $content, $tag),
            default => sprintf('<%s class="%s">%s</%s>', $tag, $class, $content, $tag),
        };
    }
}
