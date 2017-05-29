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
        $this->session = $request->session();
        $this->logger = $logger;
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
