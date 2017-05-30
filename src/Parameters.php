<?php
declare(strict_types=1);

namespace Engine;

final class Parameters
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function fetch(string $key, $default = ''): string
    {
        if ($this->exist($key)) {
            return $this->params[$key];
        } else {
            return $default;
        }
    }

    public function exist(string $key): bool
    {
        return array_key_exists($key, $this->params);
    }
}
