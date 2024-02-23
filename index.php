<?php

require_once 'vendor/autoload.php';
require_once 'Resources/functions/in.php';

use Pixelee\InsightDumper\InsightDumper;
use Pixelee\InsightDumper\Test;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$fruits = array (
    "fruits"  => array("a" => "orange", "b" => "banana", "c" => "apple"),
    "numbers" => array(1, 2, 3, 4, 5, 6),
    "holes"   => array("first", 5 => "second", "third")
);

$testClass = new Test();
$testClass->setTestProperty('yep!');

in($fruits, $testClass, false, 'test', 12.5, 6);
