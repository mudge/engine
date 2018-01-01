<?php
declare(strict_types=1);

namespace Engine;

abstract class AbstractRequest implements RequestInterface
{
    abstract public function method(): string;

    public function requestPath(): string
    {
        return explode('?', $this->requestUri(), 2)[0];
    }

    abstract public function requestUri(): string;

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
