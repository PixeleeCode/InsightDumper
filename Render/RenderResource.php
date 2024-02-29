<?php

namespace Pixelee\InsightDumper\Render;

use Pixelee\InsightDumper\HtmlRenderer;

final class RenderResource
{
    public const NOT_APPLICABLE = 'N/A';
    private const BOOL_TRUE = 'true';
    private const BOOL_FALSE = 'false';

    /**
     * Renders a resource into an HTML string, with specific handling for stream resources.
     * For PHP 8.0+ where some resources are objects, a fallback is provided.
     *
     * @param mixed $var The resource to render. Can be a resource or an object for PHP 8.0+.
     * @param int $indentLevel The current indentation level for formatting.
     * @return string HTML representation of the resource.
     */
    public static function getResource(mixed $var, int $indentLevel = 0): string
    {
        if (is_resource($var)) {
            $resourceType = get_resource_type($var);
            $resourceDetails = self::getResourceDetails($var, $indentLevel, $resourceType);
            return HtmlRenderer::wrap("insight-dump-resource", $resourceDetails);
        } elseif (is_object($var)) {
            // Fallback for PHP 8.0+ where some resources are objects
            return self::getObjectFallback($var);
        }

        return HtmlRenderer::wrap('insight-dump-resource', 'Not a resource');
    }

    /**
     * Generates detailed HTML string for a resource, including stream-specific information if applicable.
     *
     * @param resource $var The resource.
     * @param int $indentLevel The current indentation level.
     * @param string $resourceType The type of the resource.
     * @return string Detailed HTML representation of the resource.
     */
    private static function getResourceDetails($var, int $indentLevel, string $resourceType): string
    {
        $indent = str_repeat('  ', $indentLevel);
        $innerIndent = str_repeat('  ', $indentLevel + 1);
        $details = "Resource:$resourceType {\n";

        if ($resourceType === 'stream') {
            $details .= self::getStreamResourceDetails($var, $innerIndent);
        }

        $details .= "$indent}";

        return $details;
    }

    /**
     * Provides detailed HTML string for stream resources, including URI, mode, block status, and seekability.
     *
     * @param resource $var The stream resource.
     * @param string $innerIndent The indentation to use for formatting.
     * @return string Detailed HTML representation of the stream resource.
     */
    private static function getStreamResourceDetails($var, string $innerIndent): string
    {
        $meta = stream_get_meta_data($var);
        $uri = $meta['uri'] ?? self::NOT_APPLICABLE;
        $mode = $meta['mode'] ?? self::NOT_APPLICABLE;
        $blocked = $meta['blocked'] ? self::BOOL_TRUE : self::BOOL_FALSE;
        $seekable = $meta['seekable'] ? self::BOOL_TRUE : self::BOOL_FALSE;

        $details = "{$innerIndent}uri: $uri,\n";
        $details .= "{$innerIndent}mode: $mode,\n";
        $details .= "{$innerIndent}blocked: " . HtmlRenderer::wrap("insight-dump-boolean insight-dump-boolean--$blocked", $blocked) . ",\n";
        $details .= "{$innerIndent}seekable: " . HtmlRenderer::wrap("insight-dump-boolean insight-dump-boolean--$seekable", $seekable) . "\n";

        if (isset($meta['stream_type']) && $meta['stream_type'] === 'tcp_socket/ssl') {
            $crypto = var_export($meta['crypto'], true);
            $details .= "{$innerIndent}crypto: $crypto\n";
        }

        return $details;
    }

    /**
     * Provides a fallback HTML string for PHP 8.0+ resources that are represented as objects.
     *
     * @param object $var The object that is treated as a resource.
     * @return string HTML representation indicating the class of the object.
     */
    private static function getObjectFallback(object $var): string
    {
        $className = get_class($var);

        return HtmlRenderer::wrap('insight-dump-resource', "Object of class $className");
    }
}
