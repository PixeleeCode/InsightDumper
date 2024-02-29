<?php

namespace Pixelee\InsightDumper\Render;

use Pixelee\InsightDumper\HtmlRenderer;
use Pixelee\InsightDumper\Render;

final class RenderObject
{
    /**
     * Checks if the object has already been visited to prevent infinite recursion.
     *
     * @param object $var The object to check.
     * @param array &$visited Reference to the array tracking visited objects.
     * @return ?string Formatted HTML string for the visited object or null if not visited.
     */
    private static function checkVisited(object $var, array &$visited): ?string
    {
        $objectId = spl_object_id($var);
        if (isset($visited[$objectId])) {
            return self::formatVisitedObject($var, $objectId);
        }

        $visited[$objectId] = get_class($var);

        return null;
    }

    /**
     * Formats the HTML representation for an object that has already been visited.
     *
     * @param object $var The visited object.
     * @param int $objectId The object ID of the visited object.
     * @return string The formatted HTML string.
     */
    private static function formatVisitedObject(object $var, int $objectId): string
    {
        $classNameContent = HtmlRenderer::wrap('insight-dump-object', get_class($var));
        $objectIdContent = HtmlRenderer::wrap('insight-dump-object-id', "#$objectId");

        return sprintf('%s %s', $classNameContent, $objectIdContent);
    }

    /**
     * Renders the properties of an object, including static and instance properties.
     *
     * @param object $var The object whose properties are to be rendered.
     * @param int $indentLevel The current indentation level.
     * @param array &$visited Reference to the array tracking visited objects.
     * @param int $currentDepth The current depth in the recursion tree.
     * @return string The formatted HTML string of object properties.
     * @throws \ReflectionException If an error occurs during reflection.
     */
    private static function renderProperties(object $var, int $indentLevel, array &$visited, int $currentDepth): string
    {
        $reflection = new \ReflectionObject($var);
        $propsOutput = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $prefix = $property->isStatic() ? 'static ' : '';
            $propertyValue = $property->getValue($var);

            $renderedValue = Render::render($propertyValue, $indentLevel + 1, $visited, $currentDepth + 1);
            $propsOutput[] = self::formatProperty($propertyName, $renderedValue, $prefix, $indentLevel + 1);
        }

        return implode("\n", $propsOutput);
    }

    /**
     * Formats a single property of an object into an HTML string.
     *
     * @param string $propertyName The name of the property.
     * @param string $renderedValue The rendered value of the property.
     * @param string $prefix Indicates if the property is static.
     * @param int $indentLevel The current indentation level.
     * @return string The formatted HTML string for the property.
     */
    private static function formatProperty(string $propertyName, string $renderedValue, string $prefix, int $indentLevel): string
    {
        $innerIndent = str_repeat('  ', $indentLevel);

        return sprintf('%s%s%s: %s',
            $innerIndent,
            $prefix,
            HtmlRenderer::wrap('insight-dump-object-key', $propertyName),
            $renderedValue
        );
    }

    /**
     * Renders an object into an HTML string, including its properties and handling recursion.
     *
     * @param object $var The object to render.
     * @param int $indentLevel The current indentation level.
     * @param array &$visited Reference to the array tracking visited objects.
     * @param int $currentDepth The current depth in the recursion tree.
     * @return string The complete HTML representation of the object.
     * @throws \ReflectionException If an error occurs during reflection.
     */
    public static function getObject(object $var, int $indentLevel = 0, array &$visited = [], int $currentDepth = 0): string
    {
        if ($alreadyVisited = self::checkVisited($var, $visited)) {
            return $alreadyVisited;
        }

        $classNameContent = HtmlRenderer::wrap('insight-dump-object', get_class($var));
        $objectIdContent = HtmlRenderer::wrap('insight-dump-object-id', "#" . spl_object_id($var));
        $propsOutput = self::renderProperties($var, $indentLevel, $visited, $currentDepth);

        $indent = str_repeat('  ', $indentLevel);

        return "$classNameContent $objectIdContent {\n$propsOutput\n$indent}";
    }
}
