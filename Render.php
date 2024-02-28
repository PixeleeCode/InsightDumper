<?php

namespace Pixelee\InsightDumper;

use Pixelee\InsightDumper\Render\RenderDateTime;
use Pixelee\InsightDumper\Render\RenderIterable;
use Pixelee\InsightDumper\Render\RenderObject;
use Pixelee\InsightDumper\Render\RenderResource;

/**
 * The Render class is responsible for rendering the various types of PHP data
 * in a human-readable string format, similar to var_dump output, but more
 * readable and with object and resource recursion management.
 * More readable and with object and resource recursion management.
 */
final class Render
{
    /**
     * Converts a PHP variable into a human-readable HTML format.
     * Supports various data types including objects, arrays, and basic scalar types.
     *
     * @param mixed $var Variable to be rendered.
     * @param int $indentLevel Current level of indentation for formatting.
     * @param array &$visited References to objects already visited to prevent infinite recursion.
     * @param int $currentDepth Current depth in the data structure being rendered.
     * @param int $maxDepth Maximum depth to render to avoid excessively deep structures.
     * @return string Rendered HTML representation of the variable.
     * @throws \ReflectionException If reflection fails during object property access.
     */
    public static function render(mixed $var, int $indentLevel = 0, array &$visited = [], int $currentDepth = 0, int $maxDepth = 10): string
    {
        if ($currentDepth > $maxDepth) {
            return HtmlRenderer::wrap('insight-dump-max-depth', 'Max. depth reached');
        }

        $output = '';

        if (is_null($var)) {
            $output .= self::getNull();
        }
        elseif ($var instanceof \DateTime || $var instanceof \DateTimeImmutable) {
            $output .= RenderDateTime::getDateTimeInfo($var, $indentLevel);
        }
        elseif (is_string($var)) {
            $output .= self::getString($var);
        }
        elseif (is_bool($var)) {
            $output .= self::getBoolean($var);
        }
        elseif (is_int($var) || is_float($var)) {
            $output .= self::getNumber($var);
        }
        elseif (is_resource($var)) {
            $output .= RenderResource::getResource($var);
        }
        elseif (($var instanceof \Iterator || $var instanceof \IteratorAggregate)) {
            return RenderIterable::getIterable($var, $indentLevel, $visited, $currentDepth);
        }
        elseif (is_object($var)) {
            return RenderObject::getObject($var, $indentLevel, $visited, $currentDepth);
        }
        elseif (is_iterable($var)) {
            return RenderIterable::getIterable($var, $indentLevel, $visited, $currentDepth);
        }

        return $output;
    }

    /**
     * Renders a null value.
     *
     * @return string The rendered output for null.
     */
    private static function getNull(): string
    {
        return HtmlRenderer::wrap('insight-dump-null', 'null');
    }

    /**
     * Renders a string value.
     *
     * @return string The result of rendering a character string.
     */
    private static function getString(string $var): string
    {
        return HtmlRenderer::wrap('insight-dump-string', $var);
    }

    /**
     * Renders a numeric or decimal value.
     *
     * @return string The result of rendering a numeric or decimal type.
     */
    private static function getNumber(int|float $var): string
    {
        return HtmlRenderer::wrap('insight-dump-number', (string)$var);
    }

    /**
     * Renders a Boolean value.
     *
     * @return string The result of rendering a boolean.
     */
    private static function getBoolean(bool $var): string
    {
        $boolean = $var ? 'true' : 'false';

        return HtmlRenderer::wrap("insight-dump-boolean insight-dump-boolean--$boolean", $boolean);
    }

    /**
     * Checks if the given value is a string and returns it with single quotes.
     *
     * @param mixed $value The value to be checked and possibly formatted.
     * @return mixed The original string value enclosed in single quotes if it is a string; otherwise, the original value unmodified.
     */
    public static function isString(mixed $value): mixed
    {
        return is_string($value) ? "'$value'" : $value;
    }
}
