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

    public static function fromGlobals(): self
    {
        session_start();

        return new self(
            $_GET,
            $_POST,
            $_COOKIE,
            $_SESSION,
            $_SERVER
        );
    }

    public function __construct(array $get, array $post, array &$cookies, array &$session, array $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->cookies =& $cookies;
        $this->session =& $session;
        $this->server = $server;
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? '';
    }

    public function requestPath(): string
    {
        return explode('?', $this->requestUri(), 2)[0];
    }

    public function requestUri(): string
    {
        return $this->server['REQUEST_URI'] ?? '';
    }

    public function params(): Parameters
    {
        return new Parameters(array_merge($this->get, $this->post));
    }

    public function cookies(): Cookies
    {
        return new Cookies($this->cookies);
    }

    public function session(): Session
    {
        return new Session($this->session);
    }
}
