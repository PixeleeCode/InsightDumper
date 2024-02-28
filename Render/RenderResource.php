<?php

namespace Pixelee\InsightDumper\Render;

use Pixelee\InsightDumper\HtmlRenderer;

final class RenderResource
{
    /**
     * Renders a resource, with specific handling for stream resources.
     *
     * @param resource $var The resource to render.
     * @param int $indentLevel The current indentation level.
     * @return string The rendered resource.
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

    private static function getStreamResourceDetails($var, string $innerIndent): string
    {
        $meta = stream_get_meta_data($var);
        $uri = $meta['uri'] ?? 'N/A';
        $mode = $meta['mode'] ?? 'N/A';
        $blocked = $meta['blocked'] ? 'true' : 'false';
        $seekable = $meta['seekable'] ? 'true' : 'false';

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

    private static function getObjectFallback(object $var): string
    {
        $className = get_class($var);

        return HtmlRenderer::wrap('insight-dump-resource', "Object of class $className");
    }
}
