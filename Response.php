<?php

namespace Pixelee\InsightDumper;

final class Response
{
    private string $content;
    private int $statusCode;
    private array $headers;

    public function __construct(
        string $content = '',
        int $statusCode = 200,
        array $headers = ['Content-Type' => 'text/html']
    ) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function send(): self
    {
        $this->sendHeaders();
        $this->sendContent();

        return $this;
    }

    private function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value", false);
        }
    }

    private function sendContent(): void
    {
        echo "<pre>$this->content</pre>";
    }
}
