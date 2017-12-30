<?php
declare(strict_types=1);

namespace Engine;

final class Cookies
{
    private $cookies;

    public function __construct(array &$cookies)
    {
        $this->cookies =& $cookies;
    }

    public function fetch(string $key, $default = ''): string
    {
        if ($this->exist($key)) {
            return $this->cookies[$key];
        } else {
            return $default;
        }
    }

    public function exist(string $key): bool
    {
        return array_key_exists($key, $this->cookies);
    }

    public function set(string $key, string $value, int $expire = 0, string $path = '', string $domain = '', bool $secure = true, bool $httponly = true): void
    {
        if (setcookie($key, $value, $expire, $path, $domain, $secure, $httponly)) {
            $this->cookies[$key] = $value;
        }
    }

    public function delete(string $key, string $path = '', string $domain = '', bool $secure = true, bool $httponly = true): void
    {
        if (setcookie($key, '', time() - 86400, $path, $domain, $secure, $httponly)) {
            unset($this->cookies[$key]);
        }
    }
}
