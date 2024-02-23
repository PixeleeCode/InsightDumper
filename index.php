<?php

require_once 'vendor/autoload.php';
require_once 'Resources/functions/pp.php';

use Pixelee\InsightDumper\InsightDumper;

$fruits = array (
    "fruits"  => array("a" => "orange", "b" => "banana", "c" => "apple"),
    "numbers" => array(1, 2, 3, 4, 5, 6),
    "holes"   => array("first", 5 => "second", "third")
);

$stdClass = new stdClass();

pp($fruits, $stdClass);
