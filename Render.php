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
        $methods = $reflection->getMethods();

        $output = "<div class='insight-dump-object'>Object of class <strong>{$className}</strong> {<br>";

        // Affichage des propriétés
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($var);
            $propertyType = gettype($propertyValue);

            $output .= "<div class='insight-dump-property'>&nbsp;&nbsp;&nbsp;<strong>$propertyName</strong> ($propertyType): ";
            $output .= self::render($propertyValue);
            $output .= "</div>";
        }

        // Affichage des méthodes avec typage
        if (!empty($methods)) {
            $output .= "<div class='insight-dump-methods'>&nbsp;&nbsp;Methods:<br>";
            foreach ($methods as $method) {
                $methodName = $method->getName();
                $output .= "<div class='insight-dump-method'>&nbsp;&nbsp;&nbsp;&nbsp;<strong>$methodName</strong>(";

                // Paramètres de la méthode et leurs typages
                $paramsOutput = [];
                foreach ($method->getParameters() as $param) {
                    $paramType = $param->hasType() ? $param->getType() . ' ' : '';
                    $paramName = $param->getName();
                    $paramsOutput[] = "$paramType$paramName";
                }
                $output .= implode(', ', $paramsOutput);

                // Type de retour de la méthode
                if ($method->hasReturnType()) {
                    $returnType = $method->getReturnType();
                    $output .= "): " . $returnType;
                } else {
                    $output .= ")";
                }

                $output .= "</div>";
            }
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
