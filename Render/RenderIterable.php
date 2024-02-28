<?php

namespace Pixelee\InsightDumper\Render;

use Pixelee\InsightDumper\HtmlRenderer;
use Pixelee\InsightDumper\Render;

final class RenderIterable
{
    /**
     * Renders iterable data structures like arrays and objects implementing iterable interfaces.
     *
     * @param iterable $var Iterable to render.
     * @param int $indentLevel Current indentation level.
     * @param array &$visited Object references already visited.
     * @param int $currentDepth Current recursion depth.
     * @return string HTML representation of the iterable.
     */
    public static function getIterable(iterable $var, int $indentLevel = 0, array &$visited = [], int $currentDepth = 0): string
    {
        $count = self::getIterableCount($var);
        $typeLabel = self::getIterableTypeLabel($var, $count, $currentDepth);
        $content = self::getIterableContent($var, $indentLevel, $visited, $currentDepth);

        return self::wrapIterable($typeLabel, $content, $indentLevel, $currentDepth);
    }

    private static function getIterableCount(iterable $var): int
    {
        return is_countable($var) || $var instanceof \Countable ? count($var) : iterator_count($var);
    }

    private static function getIterableTypeLabel(iterable $var, int $count, int $currentDepth): string
    {
        $type = is_object($var) ? get_class((object)$var) : 'array';
        $toggleClass = $currentDepth > 1 ? 'closed' : 'opened';

        return HtmlRenderer::wrap("insight-dump-type $toggleClass insight-dump-toggle", "$type($count):");
    }

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
     * @throws \ReflectionException
     */
    private static function formatIterableItem($key, $value, iterable $var, int $indentLevel, array &$visited, int $currentDepth): string
    {
        $innerIndent = str_repeat('  ', $indentLevel + 1);
        $keyOutput = self::formatIterableKey($key, $var);
        $separator = is_object($var) ? ': ' : ' => ';
        $renderedValue = Render::render(Render::isString($value), $indentLevel + 1, $visited, $currentDepth + 1);

        return sprintf('%s%s%s%s,', $innerIndent, $keyOutput, $separator, $renderedValue);
    }

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
