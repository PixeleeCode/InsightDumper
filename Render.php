<?php

namespace Pixelee\InsightDumper;

/**
 * The Render class is responsible for rendering the various types of PHP data
 * in a human-readable string format, similar to var_dump output, but more
 * readable and with object and resource recursion management.
 * More readable and with object and resource recursion management.
 */
final class Render
{
    /**
     * Renders a PHP variable into a human-readable string.
     *
     * @param mixed $var The variable to render.
     * @param int $indentLevel The current indentation level.
     * @param array &$visited Tracks visited objects to handle recursion.
     * @return string The rendered output.
     * @throws \ReflectionException If an error occurs during reflection.
     */
    public static function render(mixed $var, int $indentLevel = 0, array &$visited = [], int $currentDepth = 0, int $maxDepth = 8): string
    {
        if ($currentDepth > $maxDepth) {
            return HtmlRenderer::wrap('insight-dump-max-depth', 'Max. depth reached');
        }

        $output = '';

        if (is_null($var)) {
            $output .= self::getNull();
        }
        elseif ($var instanceof \DateTime || $var instanceof \DateTimeImmutable) {
            $output .= self::getDateTimeInfo($var, $indentLevel);
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
            $output .= self::getResource($var);
        }
        elseif (($var instanceof \Iterator || $var instanceof \IteratorAggregate)) {
            return self::renderIterable($var, $indentLevel, $visited, $currentDepth);
        }
        elseif (is_object($var)) {
            return self::getObject($var, $indentLevel, $visited, $currentDepth);
        }
        elseif (is_iterable($var)) {
            return self::renderIterable($var, $indentLevel, $visited, $currentDepth);
        }

        return $output;
    }

    /**
     * Renders information about a DateTime or DateTimeImmutable object.
     *
     * @param \DateTime|\DateTimeImmutable $var The DateTime to render.
     * @param int $indentLevel The current indentation level.
     * @return string The rendered DateTime information.
     */
    private static function getDateTimeInfo(\DateTime|\DateTimeImmutable $var, int $indentLevel = 0): string
    {
        $indent = str_repeat('  ', $indentLevel);
        $innerIndent = str_repeat('  ', $indentLevel + 1);

        $dateString = $var->format('Y-m-d H:i:s');
        $timezone = $var->getTimezone()->getName();
        $timestamp = $var->getTimestamp();
        $dayOfWeek = $var->format('l');
        $dayOfYear = (int)$var->format('z') + 1;
        $weekOfYear = $var->format('W');
        $isLeapYear = $var->format('L') === '1' ? 'true' : 'false';
        $isLeapYearContent = HtmlRenderer::wrap("insight-dump-boolean insight-dump-boolean--$isLeapYear", $isLeapYear);
        $diffWithNow = $var->diff(new \DateTime())->format('%R%a jours');

        $output = HtmlRenderer::wrap("insight-dump-datetime", get_class($var)) ." {\n";
        $output .= "{$innerIndent}datetime: $dateString,\n";
        $output .= "{$innerIndent}timezone: $timezone,\n";
        $output .= "{$innerIndent}timestamp: $timestamp,\n";
        $output .= "{$innerIndent}dayOfWeek: $dayOfWeek,\n";
        $output .= "{$innerIndent}dayOfYear: $dayOfYear,\n";
        $output .= "{$innerIndent}weekOfYear: $weekOfYear,\n";
        $output .= "{$innerIndent}isLeapYear: $isLeapYearContent,\n";
        $output .= "{$innerIndent}diffWithNow: $diffWithNow\n";
        $output .= "$indent}";

        return HtmlRenderer::wrap("insight-dump-datetime-content", $output);
    }

    /**
     * Renders a null value.
     *
     * @return string The rendered output for null.
     */
    private static function getNull(): string
    {
        return HtmlRenderer::wrap("insight-dump-null", "null");
    }

    /**
     * Renders an object, including handling for recursion and object references.
     *
     * @param object $var The object to render.
     * @param int $indentLevel The current indentation level.
     * @param array &$visited Tracks visited objects to handle recursion.
     * @return string The rendered object.
     * @throws \ReflectionException If an error occurs during reflection.
     */
    private static function getObject(object $var, int $indentLevel = 0, array &$visited = [], int $currentDepth = 0): string
    {
        $className = get_class($var);
        $objectId = spl_object_id($var);

        if (isset($visited[$objectId])) {
            $classNameContent = HtmlRenderer::wrap("insight-dump-object", $className);
            $objectIdContent = HtmlRenderer::wrap("insight-dump-object-id", "#$objectId");

            return sprintf('%s %s', $classNameContent, $objectIdContent);
        }

        $visited[$objectId] = $className;

        $reflection = new \ReflectionObject($var);
        $className = $reflection->getName();
        $properties = $reflection->getProperties();
        $indent = str_repeat('  ', $indentLevel);

        $classNameContent = HtmlRenderer::wrap("insight-dump-object", $className);
        $objectIdContent = HtmlRenderer::wrap("insight-dump-object-id", "#$objectId");

        if (empty($properties)) {
            return "$classNameContent::class";
        }

        $innerIndent = str_repeat('  ', $indentLevel + 1);
        $output = "$classNameContent $objectIdContent {\n";
        $propsOutput = [];

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $prefix = $property->isStatic() ? 'static ' : '';
            $propertyValue = $property->isStatic()
                ? $property->getValue()
                : $property->getValue($var)
            ;

            $renderedValue = self::render($propertyValue, $indentLevel + 1, $visited, $currentDepth + 1);
            $propsOutput[] = sprintf('%s%s<span class="insight-dump-object-key">%s</span>: %s,', $innerIndent, $prefix, $propertyName, self::isString($renderedValue));
        }

        if (!empty($propsOutput)) {
            $lastIndex = count($propsOutput) - 1;
            $propsOutput[$lastIndex] = rtrim($propsOutput[$lastIndex], ',');
        }

        $output .= implode("\n", $propsOutput);
        $output .= "\n$indent}";

        return $output;
    }

    /**
     * Renders a resource, with specific handling for stream resources.
     *
     * @param resource $var The resource to render.
     * @param int $indentLevel The current indentation level.
     * @return string The rendered resource.
     */
    private static function getResource(mixed $var, int $indentLevel = 0): string
    {
        if (is_resource($var)) {
            $type = get_resource_type($var);
            $indent = str_repeat('  ', $indentLevel);
            $innerIndent = str_repeat('  ', $indentLevel + 1);
            $output = "Resource:$type {\n";

            if ($type === 'stream' && $meta = stream_get_meta_data($var)) {
                $uri = $meta['uri'] ?? 'N/A';
                $mode = $meta['mode'] ?? 'N/A';
                $blocked = $meta['blocked'] ? 'true' : 'false';
                $seekable = $meta['seekable'] ? 'true' : 'false';

                $blockedContent = HtmlRenderer::wrap("insight-dump-boolean insight-dump-boolean--$blocked", $blocked);
                $seekableContent = HtmlRenderer::wrap("insight-dump-boolean insight-dump-boolean--$seekable", $seekable);

                $output .= "{$innerIndent}uri: $uri,\n";
                $output .= "{$innerIndent}mode: $mode,\n";
                $output .= "{$innerIndent}blocked: $blockedContent,\n";
                $output .= "{$innerIndent}seekable: $seekableContent\n";

                if (isset($meta['stream_type']) && $meta['stream_type'] === 'tcp_socket/ssl') {
                    $crypto = var_export($meta['crypto'], true);
                    $output .= "{$innerIndent}crypto: $crypto\n";
                }
            }

            $output .= "$indent}";

            return HtmlRenderer::wrap("insight-dump-resource", $output);
        }

        // Fallback si l'argument n'est pas une ressource (utile pour PHP 8.0+ où certaines "ressources" sont des objets)
        if (is_object($var)) {
            $className = get_class($var);
            return HtmlRenderer::wrap("insight-dump-resource", "Object of class $className");
        }

        return HtmlRenderer::wrap("insight-dump-resource", 'Not a ressource');
    }

    /**
     * Renders an iterable, including arrays and objects implementing iterable interfaces.
     *
     * @param iterable $var The iterable to render.
     * @param int $indentLevel The current indentation level.
     * @param array &$visited Tracks visited objects to handle recursion.
     * @return string The rendered iterable.
     * @throws \ReflectionException If an error occurs during reflection.
     */
    private static function renderIterable(iterable $var, int $indentLevel = 0, array &$visited = [], int $currentDepth = 0): string
    {
        $indent = str_repeat('  ', $indentLevel);
        $innerIndent = $indent . '  ';
        $count = is_countable($var) || $var instanceof \Countable ? count($var) : iterator_count($var);
        $getType = is_object($var) ? get_class((object)$var) : 'array';
        $getTypeContent = HtmlRenderer::wrap("insight-dump-type", "$getType($count):");
        $output = "$getTypeContent [";

        if ($count === 0) {
            $output .= ' ]';
        } else {
            $output .= "\n";
            $itemsOutput = [];

            foreach ($var as $key => $value) {
                $renderedValue = self::render(self::isString($value), $indentLevel + 1, $visited, $currentDepth + 1);

                if (is_string($key)) {
                    if (is_object($var)) {
                        $keyOutput = HtmlRenderer::wrap("insight-dump-object-key", $key);
                    } else {
                        $keyOutput = "'" . HtmlRenderer::wrap("insight-dump-string", $key) . "'";
                    }
                } else {
                    $keyOutput = HtmlRenderer::wrap("insight-dump-array-key", $key);
                }

                $separator = is_object($var) ? ': ' : ' => ';

                $itemsOutput[] = sprintf('%s%s%s%s,', $innerIndent, $keyOutput, $separator, $renderedValue);
            }

            if (!empty($itemsOutput)) {
                $lastIndex = count($itemsOutput) - 1;
                $itemsOutput[$lastIndex] = rtrim($itemsOutput[$lastIndex], ',');
            }

            $output .= implode("\n", $itemsOutput);
            $output .= "\n$indent]";
        }

        return $output;
    }

    /**
     * Renders a string value.
     *
     * @return string The result of rendering a character string.
     */
    private static function getString(string $var): string
    {
        return HtmlRenderer::wrap("insight-dump-string", $var);
    }

    /**
     * Renders a numeric or decimal value.
     *
     * @return string The result of rendering a numeric or decimal type.
     */
    private static function getNumber(int|float $var): string
    {
        return HtmlRenderer::wrap("insight-dump-number", (string)$var);
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
    private static function isString(mixed $value): mixed
    {
        return is_string($value) ? "'$value'" : $value;
    }
}
