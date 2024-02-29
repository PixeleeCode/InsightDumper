<?php

namespace Pixelee\InsightDumper\Render;

use Pixelee\InsightDumper\HtmlRenderer;
use Pixelee\InsightDumper\Render;

final class RenderIterable
{
    /**
     * Renders iterable data structures such as arrays and objects that implement the iterable interface.
     *
     * @param iterable $var The iterable variable to be rendered.
     * @param int $indentLevel The current level of indentation for formatting.
     * @param array &$visited A reference to an array keeping track of visited objects to avoid infinite loops.
     * @param int $currentDepth The current depth of recursion.
     * @return string The HTML representation of the iterable.
     */
    public static function getIterable(iterable $var, int $indentLevel = 0, array &$visited = [], int $currentDepth = 0): string
    {
        $count = self::getIterableCount($var);
        $typeLabel = self::getIterableTypeLabel($var, $count, $currentDepth);
        $content = self::getIterableContent($var, $indentLevel, $visited, $currentDepth);

        return self::wrapIterable($typeLabel, $content, $indentLevel, $currentDepth);
    }

    /**
     * Calculates the count of elements in an iterable.
     *
     * @param iterable $var The iterable to count.
     * @return int The count of elements.
     */
    private static function getIterableCount(iterable $var): int
    {
        return is_countable($var) || $var instanceof \Countable ? count($var) : iterator_count($var);
    }

    /**
     * Generates the label for an iterable type, including its class name and count, with a toggle class indicating its opened or closed state.
     *
     * @param iterable $var The iterable variable.
     * @param int $count The count of elements in the iterable.
     * @param int $currentDepth The current depth of recursion.
     * @return string The HTML representation of the type label.
     */
    private static function getIterableTypeLabel(iterable $var, int $count, int $currentDepth): string
    {
        $type = is_object($var) ? get_class((object)$var) : 'array';
        $toggleClass = $currentDepth > 1 ? 'closed' : 'opened';

        return HtmlRenderer::wrap("insight-dump-type $toggleClass insight-dump-toggle", "$type($count):");
    }

    /**
     * Generates the HTML content for an iterable, formatting each item within.
     *
     * @param iterable $var The iterable variable.
     * @param int $indentLevel The current level of indentation for formatting.
     * @param array &$visited A reference to an array keeping track of visited objects to avoid infinite loops.
     * @param int $currentDepth The current depth of recursion.
     * @return string The HTML content for the iterable.
     * @throws \ReflectionException
     */
    private static function getIterableContent(iterable $var, int $indentLevel, array &$visited, int $currentDepth): string
    {
        if (self::getIterableCount($var) === 0) {
            return '';
        }

        if ($var instanceof \ArrayObject) {
            $var = $var->getArrayCopy();
        }

        $itemsOutput = array_map(
            /** @throws \ReflectionException */
            static function ($key, $value) use ($var, $indentLevel, &$visited, $currentDepth) {
                return self::formatIterableItem($key, $value, $var, $indentLevel, $visited, $currentDepth);
            },
            array_keys($var),
            $var
        );

        if (!empty($itemsOutput)) {
            $lastIndex = count($itemsOutput) - 1;
            $itemsOutput[$lastIndex] = rtrim($itemsOutput[$lastIndex], ',');
        }

        return implode("\n", $itemsOutput);
    }

    /**
     * Formats a single item within an iterable, applying proper HTML wrapping.
     *
     * @param mixed $key The key of the item.
     * @param mixed $value The value of the item.
     * @param iterable $var The parent iterable.
     * @param int $indentLevel The current level of indentation for formatting.
     * @param array &$visited A reference to an array keeping track of visited objects to avoid infinite loops.
     * @param int $currentDepth The current depth of recursion.
     * @return string The formatted HTML string for the item.
     * @throws \ReflectionException
     */
    private static function formatIterableItem(mixed $key, mixed $value, iterable $var, int $indentLevel, array &$visited, int $currentDepth): string
    {
        $innerIndent = str_repeat('  ', $indentLevel + 1);
        $keyOutput = self::formatIterableKey($key, $var);
        $separator = is_object($var) ? ': ' : ' => ';
        $renderedValue = Render::render(Render::isString($value), $indentLevel + 1, $visited, $currentDepth + 1);

        return sprintf('%s%s%s%s,', $innerIndent, $keyOutput, $separator, $renderedValue);
    }

    /**
     * Formats the key of an item in an iterable, considering if it's an object or array to apply appropriate HTML wrapping.
     *
     * @param string|int $key The key of the item.
     * @param iterable $var The parent iterable.
     * @return string The formatted HTML string for the key.
     */
    private static function formatIterableKey(string|int $key, iterable $var): string
    {
        if (is_string($key)) {
            return is_object($var)
                ? HtmlRenderer::wrap('insight-dump-object-key', $key)
                : "'" . HtmlRenderer::wrap('insight-dump-string', $key) . "'"
            ;
        }

        return HtmlRenderer::wrap('insight-dump-array-key', $key);
    }

    /**
     * Wraps the entire iterable content in HTML, adjusting the toggle class based on the recursion depth.
     *
     * @param string $typeLabel The label for the iterable type.
     * @param string $content The HTML content of the iterable.
     * @param int $indentLevel The current level of indentation for formatting.
     * @param int $currentDepth The current depth of recursion.
     * @return string The fully wrapped HTML representation of the iterable.
     */
    private static function wrapIterable(string $typeLabel, string $content, int $indentLevel, int $currentDepth): string
    {
        $indent = str_repeat('  ', $indentLevel);
        if ($content === '') {
            return $typeLabel . ' []';
        }

        $toggleClass = $currentDepth > 1 ? 'closed' : 'opened';
        $openTag = HtmlRenderer::wrap("insight-dump-array-content-$toggleClass", '', 'span', HtmlRenderer::OPEN_TAG);
        $closeTag = HtmlRenderer::wrap('insight-dump-array-content', '', 'span', HtmlRenderer::CLOSE_TAG);

        return $typeLabel . ' [' . $openTag . "\n" . $content . "\n" . $indent . $closeTag . ']';
    }
}
