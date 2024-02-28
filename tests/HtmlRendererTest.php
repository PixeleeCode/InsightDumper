<?php

use PHPUnit\Framework\TestCase;
use Pixelee\InsightDumper\HtmlRenderer;

final class HtmlRendererTest extends TestCase
{
    public function testWrapWithDefaultTag(): void
    {
        $html = HtmlRenderer::wrap('test-class', 'Test Content');
        $this->assertSame('<span class="test-class">Test Content</span>', $html);
    }

    public function testWrapWithCustomTag(): void
    {
        $html = HtmlRenderer::wrap('test-class', 'Test Content', 'div');
        $this->assertSame('<div class="test-class">Test Content</div>', $html);
    }

    public function testWrapWithSpecialCharacters(): void
    {
        $html = HtmlRenderer::wrap('test-class', 'Content & <"Content">', 'div');
        $this->assertSame('<div class="test-class">Content & <"Content"></div>', $html);
    }

    public function testWrapWithEmptyContent(): void
    {
        $html = HtmlRenderer::wrap('test-class', '', 'div');
        $this->assertSame('<div class="test-class"></div>', $html);
    }

    public function testWrapWithEmptyClass(): void
    {
        $html = HtmlRenderer::wrap('', 'Test Content', 'div');
        $this->assertSame('<div class="">Test Content</div>', $html);
    }

    public function testWrapWithEmptyTag(): void
    {
        $html = HtmlRenderer::wrap('test-class', 'Test Content', '');
        $this->assertSame('<span class="test-class">Test Content</span>', $html);
    }
}
