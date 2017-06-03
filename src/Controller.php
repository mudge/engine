<?php
declare(strict_types=1);

namespace Engine;

use Psr\Log\LoggerInterface;

abstract class Controller
{
    public function __construct(Request $request, Response $response, LoggerInterface $logger)
    {
        $this->request = $request;
        $this->response = $response;
        $this->logger = $logger;

        $this->session = $request->session();
    }

    public function renderForm(string $template, array $variables = []): void
    {
        $this->render($template, array_merge($variables, ['csrf_token' => $this->csrfToken()]));
    }

    public function render(string $template, array $variables = []): void
    {
        $this->response->render($template, $variables);
    }

    public function redirect(string $location): void
    {
        $this->response->redirect($location);
    }

    public function csrfToken(): string
    {
        return $this->session->csrfToken();
    }

    public function verifyCsrfToken(): void
    {
        $csrfToken = $this->request->params()->fetch('csrf_token');
        if (!$this->session->verifyCsrfToken($csrfToken)) {
            $this->logger->warning("Invalid CSRF token: {$csrfToken}");
            $this->session->reset();
            $this->response->forbidden();
        }
    }
}
