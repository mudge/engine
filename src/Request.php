<?php
declare(strict_types=1);

namespace Engine;

final class Request
{
    private $get;
    private $post;
    private $cookies;
    private $session;
    private $server;

    public function __construct(array $get, array $post, array &$cookies, array &$session, array $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->cookies =& $cookies;
        $this->session =& $session;
        $this->server = $server;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function requestUri(): string
    {
        return $this->server['REQUEST_URI'];
    }

    public function path(): string
    {
        return "{$this->scriptName()}{$this->pathInfo()}";
    }

    public function scriptName(): string
    {
        return $this->server['SCRIPT_NAME'] ?? '';
    }

    public function pathInfo(): string
    {
        return $this->server['PATH_INFO'] ?? '';
    }

    public function params(): Parameters
    {
        return new Parameters(array_merge($this->get, $this->post));
    }

    public function &cookies(): array
    {
        return $this->cookies;
    }

    public function session(): Session
    {
        return new Session($this->session);
    }
}
