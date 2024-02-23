<?php

namespace Pixelee\InsightDumper;

final class Render
{
    public static function render(mixed $var): string
    {
        $output = '';

        if (is_null($var)) {
            $output .= self::getNull();
        }

        if (is_object($var)) {
            $output .= self::getObject($var);
        }

        if (is_array($var)) {
            $output .= self::getArray($var);
        }

        if (is_string($var)) {
            $output .= self::getString($var);
        }

        if (is_int($var) || is_float($var)) {
            $output .= self::getNumber($var);
        }

        if (is_bool($var)) {
            $output .= self::getBoolean($var);
        }

        return $output;
    }

    private static function getNull(): string
    {
        return '<span class="insight-dump-null">null</span>';
    }

    private static function getObject(object $var): string
    {
        $reflection = new \ReflectionObject($var);
        $className = $reflection->getName();
        $properties = $reflection->getProperties();

        $output = "<div class='insight-dump-object'>Object of class <strong>{$className}</strong> {";

        foreach ($properties as $property) {
            $property->setAccessible(true); // Permet d'accéder aux propriétés privées et protégées
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($var); // Récupère la valeur de la propriété
            $propertyType = gettype($propertyValue);

            // Pour un affichage plus sophistiqué, vous pouvez appeler `self::render()` sur `$propertyValue`
            $output .= "<div class='insight-dump-property'>{$propertyName} ({$propertyType}): ";
            $output .= self::render($propertyValue);
            $output .= "</div>";
        }

        $output .= "}</div>";

        return $output;
    }

    private static function getArray(array $var): string
    {
        $output = '<div class="insight-dump-array">Array(';

        foreach ($var as $key => $value) {
            $output .= '<div class="insight-dump-key">'. $key .'</div> => ';
            $output .= self::render($value);
        }

        $output .= ')</div>';

        return $output;
    }

    private static function getString(string $var): string
    {
        return '<span class="insight-dump-string">'. $var .'</span>';
    }

    private static function getNumber(int|float $var): string
    {
        return '<span class="insight-dump-number">'. $var .'</span>';
    }

    private static function getBoolean(bool $var): string
    {
        $boolean = $var ? 'true' : 'false';
        return '<span class="insight-dump-boolean insight-dump-boolean--'. $boolean.'">'. $boolean .'</span>';
    }
}
