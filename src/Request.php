<?php
declare(strict_types=1);

namespace Engine;

final class Request extends AbstractRequest
{
    protected $get;
    protected $post;
    protected $cookies;
    protected $session;
    protected $server;

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

    public function requestUri(): string
    {
        return $this->server['REQUEST_URI'] ?? '';
    }
}
