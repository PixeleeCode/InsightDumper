<?php

namespace Pixelee\InsightDumper;

require_once __DIR__.'/Resources/functions/pp.php';

final class InsightDumper
{
    public static function dump(mixed $vars): mixed
    {
        if (is_array($vars)) {

            echo '<div style="color: blue;">Array(' . count($vars) . ')</div><div style="margin-left: 20px;">';

            foreach ($vars as $key => $value) {
                echo '<div style="color: green;">[' . $key . '] => </div>';
                self::dump($value);
            }

            echo '</div>';
        } elseif (is_object($vars)) {
            echo '<div style="color: #9400D3;">Object(' . get_class($vars) . ')</div><div style="margin-left: 20px;">';
            foreach (get_object_vars($vars) as $key => $value) {
                echo '<div style="color: green;">' . $key . ' => </div>';
                self::dump($value);
            }
            echo '</div>';
        } else {
            echo '<div style="color: red;">' . htmlspecialchars(print_r($vars, true)) . '</div>';
        }

        return $vars;
    }
}
