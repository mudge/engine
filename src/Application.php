<?php
declare(strict_types=1);

namespace Engine;

use Psr\Log\LoggerInterface;

final class Application
{
    public $router;
    private $name;
    private $twig;
    private $logger;

    public function __construct(string $name, \Twig_Environment $twig, LoggerInterface $logger)
    {
        $this->name = $name;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->router = new Router($logger);
    }

    public function request(): Request
    {
        session_name($this->name);
        session_start();

        return new Request(
            $_GET,
            $_POST,
            $_COOKIE,
            $_SESSION,
            $_SERVER
        );
    }

    public function response(): Response
    {
        return new Response($this->twig, $this->logger);
    }
}
