<?php

namespace Pixelee\InsightDumper\Render;

use Pixelee\InsightDumper\HtmlRenderer;

final class RenderDateTime
{
    /**
     * Renders detailed information for DateTime or DateTimeImmutable objects.
     *
     * @param \DateTime|\DateTimeImmutable $var The DateTime object to render.
     * @param int $indentLevel The current indentation level for formatting.
     * @return string The HTML representation of DateTime information.
     */
    public static function getDateTimeInfo(\DateTime|\DateTimeImmutable $var, int $indentLevel = 0): string
    {
        // Basic information setup
        $indent = str_repeat('  ', $indentLevel);
        $output = HtmlRenderer::wrap("insight-dump-datetime", get_class($var)) . " {\n";

        // Append each piece of DateTime information
        $output .= self::formatDateTimeInfo('datetime', $var->format('Y-m-d H:i:s'), $indentLevel);
        $output .= self::formatDateTimeInfo('timezone', $var->getTimezone()->getName(), $indentLevel);
        $output .= self::formatDateTimeInfo('timestamp', $var->getTimestamp(), $indentLevel);
        $output .= self::formatDateTimeInfo('dayOfWeek', $var->format('l'), $indentLevel);
        $output .= self::formatDateTimeInfo('dayOfYear', (int)$var->format('z') + 1, $indentLevel);
        $output .= self::formatDateTimeInfo('weekOfYear', $var->format('W'), $indentLevel);
        $output .= self::formatDateTimeInfo('isLeapYear', $var->format('L') === '1' ? 'true' : 'false', $indentLevel, true);
        $output .= self::formatDateTimeInfo('diffWithNow', $var->diff(new \DateTime())->format('%R%a jours'), $indentLevel);

        // Close the HTML structure
        $output .= "$indent}";

        return HtmlRenderer::wrap("insight-dump-datetime-content", $output);
    }

    /**
     * Formats a single piece of DateTime information into an HTML string.
     *
     * @param string $name The name of the information piece.
     * @param mixed $value The value of the information piece.
     * @param int $indentLevel The current indentation level.
     * @param bool $isBoolean Indicates if the value is a boolean (for special wrapping).
     * @return string The formatted HTML string.
     */
    private static function formatDateTimeInfo(string $name, string|int|bool $value, int $indentLevel, bool $isBoolean = false): string
    {
        $innerIndent = str_repeat('  ', $indentLevel + 1);
        if ($isBoolean) {
            $value = HtmlRenderer::wrap("insight-dump-boolean insight-dump-boolean--$value", $value);
        }

        return sprintf("%s%s: %s,\n", $innerIndent, $name, $value);
    }
}
