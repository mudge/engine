<?php
declare(strict_types=1);

namespace Engine;

use Psr\Log\LoggerInterface;

final class Response extends AbstractResponse
{
    public function render(string $template, array $variables = []): void
    {
        $this->sendHeaders();

        $this->logger->debug("Rendering template {$template}");
        echo $this->twig->render($template, $variables);
    }

    public function sendHeaders(): void
    {
        $this->header('X-Content-Type-Options: nosniff');
        $this->header('X-XSS-Protection: 1; mode=block');
        $this->header('X-Frame-Options: DENY');
        $this->header('Referrer-Policy: no-referrer');
    }

    public function header(string $header): void
    {
        header($header);
    }
}
