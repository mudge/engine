<?php
declare(strict_types=1);

namespace Engine;

use Psr\Log\LoggerInterface;

final class Response
{
    public function __construct(\Twig_Environment $twig, LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function notFound(): void
    {
        $this->header('HTTP/1.0 404 Not Found');
        $this->render('404.html');
    }

    public function forbidden(): void
    {
        $this->header('HTTP/1.0 403 Forbidden');
        $this->render('403.html');
    }

    public function methodNotAllowed(): void
    {
        $this->header('HTTP/1.0 405 Method Not Allowed');
        $this->render('405.html');
    }

    public function redirect(string $location): void
    {
        $this->header("Location: {$location}");
    }

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
