<?php
declare(strict_types=1);

namespace Engine;

interface ResponseInterface
{
    public function notFound(): void;
    public function forbidden(): void;
    public function methodNotAllowed(): void;
    public function redirect(string $location): void;
    public function render(string $template, array $variables = []): void;
    public function header(string $header): void;
}
