<?php
declare(strict_types=1);

namespace Engine;

final class TestRequest extends AbstractRequest
{
    public $get;
    public $post;
    public $cookies;
    public $session;

    public function __construct(string $method, string $uri, array $get = [], $post = [], array $cookies = [], array $session = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->get = $get;
        $this->post = $post;
        $this->cookies = $cookies;
        $this->session = $session;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function requestUri(): string
    {
        return $this->uri;
    }
}
