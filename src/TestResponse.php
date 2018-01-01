<?php
declare(strict_types=1);

namespace Engine;

use Psr\Log\LoggerInterface;

final class TestResponse extends AbstractResponse
{
    public $headers;
    public $body;

    public function __construct(\Twig_Environment $twig, LoggerInterface $logger)
    {
        parent::__construct($twig, $logger);

        $this->headers = [];
        $this->body = '';
    }

    public function render(string $template, array $variables = []): void
    {
        $this->body .= $this->twig->render($template, $variables);
    }

    public function header(string $header): void
    {
        $this->headers[] = $header;
    }
}
