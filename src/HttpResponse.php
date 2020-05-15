<?php

namespace XTAIN\DeutschePostPortokasse;

use Symfony\Component\BrowserKit\Response;

class HttpResponse
{

    protected $content;

    protected $statusCode;

    protected $headers = [];

    public function __construct(string $content, int $statusCode, array $headers)
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Gets the response content.
     *
     * @return string The response content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Gets a response header.
     *
     * @return string|array The first header value if $first is true, an array of values otherwise
     */
    public function getHeader(string $header, bool $first = true)
    {
        if (!isset($this->headers[$header])) {
            return null;
        }

        $headers = $this->headers[$header];

        if ($first) {
            return $headers[0];
        }

        return $headers;
    }
}
