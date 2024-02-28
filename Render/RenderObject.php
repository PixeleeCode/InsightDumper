<?php

namespace Pixelee\InsightDumper\Render;

use Pixelee\InsightDumper\HtmlRenderer;
use Pixelee\InsightDumper\Render;

final class RenderObject
{
    private static function checkVisited(object $var, array &$visited): ?string
    {
        $objectId = spl_object_id($var);
        if (isset($visited[$objectId])) {
            return self::formatVisitedObject($var, $objectId);
        }

        $visited[$objectId] = get_class($var);

        return null;
    }

    private static function formatVisitedObject(object $var, int $objectId): string
    {
        $classNameContent = HtmlRenderer::wrap('insight-dump-object', get_class($var));
        $objectIdContent = HtmlRenderer::wrap('insight-dump-object-id', "#$objectId");

        return sprintf('%s %s', $classNameContent, $objectIdContent);
    }

    /**
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
