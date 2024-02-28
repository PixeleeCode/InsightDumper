<?php

use PHPUnit\Framework\TestCase;
use Pixelee\InsightDumper\InsightDumper;

final class InsightDumperTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testDump(): void
    {
        // Prepare
        $testData = ['key' => 'value'];
        $file = 'testFile.php';
        $line = 123;
        $executionTime = 0.0012;

        // Execute
        $output = InsightDumper::dump($testData, $file, $line, $executionTime);

        // Assert
        $this->assertStringContainsString('<link rel="stylesheet" type="text/css" href="Resources/css/insight-dumper.css">', $output);
        $this->assertStringContainsString('<div class="insight-dump-wrapper">', $output);
        $this->assertStringContainsString($file, $output);
        $this->assertStringContainsString((string)$line, $output);
        $this->assertStringContainsString(sprintf("%.4f seconds", $executionTime), $output);
        $this->assertStringContainsString('key', $output);
    }

    /**
     * @throws ReflectionException
     */
    public function testCircularReference(): void
    {
        $object1 = new stdClass();
        $object2 = new stdClass();
        $object1->ref = $object2;
        $object2->ref = $object1;

        $output = InsightDumper::dump($object1);
        $this->assertStringContainsString('ref', $output);
    }

    /**
     * @throws ReflectionException
     */
    public function testLargeData(): void
    {
        $largeArray = range(1, 10000);
        $output = InsightDumper::dump($largeArray);
        $this->assertStringContainsString('10000', $output);
    }

    /**
     * @throws ReflectionException
     */
    public function testExecutionInfo(): void
    {
        $output = InsightDumper::dump('test', __FILE__, __LINE__);
        $this->assertStringContainsString(__FILE__, $output);
        $this->assertStringContainsString('60', $output);
    }
}
