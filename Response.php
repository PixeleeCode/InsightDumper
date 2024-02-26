<?php

namespace Pixelee\InsightDumper;

/**
 * The Response class is responsible for encapsulating HTTP response data,
 * including content, status code, and headers. It provides methods to send
 * these responses back to the client.
 */
final class Response
{
    /**
     * The content of the HTTP response.
     *
     * @var string
     */
    private string $content;

    /**
     * The HTTP status code for the response.
     *
     * @var int
     */
    private int $statusCode;

    /**
     * Headers to be sent along with the response.
     *
     * @var array
     */
    private array $headers;

    /**
     * Constructs a new Response instance with given content, status code, and headers.
     *
     * @param string $content The content to send in the response body.
     * @param int $statusCode The HTTP status code for the response (default: 200).
     * @param array $headers An associative array of headers to send with the response.
     */
    public function __construct(
        string $content = '',
        int $statusCode = 200,
        array $headers = ['Content-Type' => 'text/html']
    ) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Sends the response to the client.
     *
     * This method sends the prepared HTTP headers and content to the client.
     * It first checks if headers have already been sent to avoid PHP errors
     * and sets the response status code. It then sends each header and finally
     * the content.
     *
     * @return self Returns $this to allow method chaining.
     */
    public function send(): self
    {
        $this->sendHeaders();
        $this->sendContent();

        return $this;
    }

    /**
     * Sends the HTTP headers for the response.
     *
     * If headers have already been sent, this method does nothing. Otherwise,
     * it sets the response's status code and iterates over the $headers property
     * to send each header using PHP's header() function.
     */
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

    /**
     * Sends the content of the response.
     *
     * This method outputs the response content. The content is wrapped in <pre>
     * tags to preserve formatting, making it suitable for displaying preformatted
     * text or code.
     */
    private function sendContent(): void
    {
        echo "<pre>$this->content</pre>";
    }
}
