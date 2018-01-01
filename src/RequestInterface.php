<?php
declare(strict_types=1);

namespace Engine;

interface RequestInterface
{
    public function method(): string;
    public function requestPath(): string;
    public function requestUri(): string;
    public function params(): Parameters;
    public function cookies(): Cookies;
    public function session(): Session;
}
