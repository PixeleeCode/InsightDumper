<?php

use PHPUnit\Framework\TestCase;
use Pixelee\InsightDumper\Render;

final class RenderTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testRenderString(): void
    {
        $result = Render::render("test string");
        $expected = "<span class=\"insight-dump-string\">test string</span>";
        $this->assertEquals($expected, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRenderInteger(): void
    {
        $result = Render::render(123);
        $expected = "<span class=\"insight-dump-number\">123</span>";
        $this->assertEquals($expected, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRenderBooleanTrue(): void
    {
        $result = Render::render(true);
        $expected = "<span class=\"insight-dump-boolean insight-dump-boolean--true\">true</span>";
        $this->assertEquals($expected, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRenderBooleanFalse(): void
    {
        $result = Render::render(false);
        $expected = "<span class=\"insight-dump-boolean insight-dump-boolean--false\">false</span>";
        $this->assertEquals($expected, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRenderObject(): void
    {
        $object = new stdClass();
        $object->property = 'value';
        $result = Render::render($object);

        $this->assertStringContainsString('<span class="insight-dump-object">stdClass</span>', $result);
        $this->assertStringContainsString('<span class="insight-dump-object-key">property</span>', $result);
        $this->assertStringContainsString('<span class="insight-dump-string">value</span>', $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRenderArray(): void
    {
        $array = ['key' => 'value'];
        $result = Render::render($array);
        $expected = "<span class=\"insight-dump-type\">array(1):</span> [\n  '<span class=\"insight-dump-string\">key</span>' => <span class=\"insight-dump-string\">'value'</span>\n]";
        $this->assertEquals($expected, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRenderResource(): void
    {
        $resource = fopen("php://memory", 'rb');
        $result = Render::render($resource);
        fclose($resource);

        $this->assertStringContainsString('Resource:stream', $result);
        $this->assertStringContainsString('uri: php://memory', $result);
        $this->assertStringContainsString('mode: rb', $result);
        $this->assertStringContainsString('blocked: <span class="insight-dump-boolean insight-dump-boolean--true">true</span>', $result);
        $this->assertStringContainsString('seekable: <span class="insight-dump-boolean insight-dump-boolean--true">true</span>', $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRenderRecursion(): void
    {
        $object = new stdClass();
        $object->self = $object;
        $visited = [];
        $result = Render::render($object, 0, $visited);

        $this->assertStringContainsString('stdClass', $result);
        $this->assertStringContainsString('self', $result);
    }
}
