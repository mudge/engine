<?php
declare(strict_types=1);

namespace Engine;

final class Session
{
    private $session;

    public function __construct(array &$session)
    {
        $this->session =& $session;
    }

    public function csrfToken(): string
    {
        if ($this->exist('csrf_token')) {
            return $this->fetch('csrf_token');
        }

        $token = bin2hex(random_bytes(64));
        $this->set('csrf_token', $token);

        return $token;
    }

    public function verifyCsrfToken(string $token): bool
    {
        return hash_equals($token, $this->fetch('csrf_token'));
    }

    public function fetch(string $key, $default = ''): string
    {
        if ($this->exist($key)) {
            return $this->session[$key];
        } else {
            return $default;
        }
    }

    public function exist(string $key): bool
    {
        return array_key_exists($key, $this->session);
    }

    public function set(string $key, string $value): void
    {
        $this->session[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }

    public function reset(): void
    {
        foreach ($this->session as $key => $value) {
            unset($this->session[$key]);
        }
    }
}
