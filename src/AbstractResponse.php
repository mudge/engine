<?php
declare(strict_types=1);

namespace Engine;

use Psr\Log\{LoggerInterface, NullLogger};

abstract class AbstractResponse implements ResponseInterface
{
    public function __construct(\Twig_Environment $twig, LoggerInterface $logger = null)
    {
        $this->twig = $twig;

        if ($logger === null) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }
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

    abstract public function render(string $template, array $variables = []): void;
    abstract public function header(string $header): void;
}
