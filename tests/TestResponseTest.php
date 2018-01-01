<?php
declare(strict_types=1);

namespace Engine;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class TestResponseTest extends TestCase
{
    public function testNotFoundSends404Header(): void
    {
        $response = $this->buildResponse();
        $response->notFound();

        $this->assertContains('HTTP/1.0 404 Not Found', $response->headers);
    }

    public function testNotFoundRenders404Template(): void
    {
        $response = $this->buildResponse();
        $response->notFound();

        $this->assertContains('Page Not Found', $response->body);
    }

    public function testForbiddenSends403Header(): void
    {
        $response = $this->buildResponse();
        $response->forbidden();

        $this->assertContains('HTTP/1.0 403 Forbidden', $response->headers);
    }

    public function testForbiddenRenders403Template(): void
    {
        $response = $this->buildResponse();
        $response->forbidden();

        $this->assertContains('Access Denied', $response->body);
    }

    public function testMethodNotAllowedSends405Header(): void
    {
        $response = $this->buildResponse();
        $response->methodNotAllowed();

        $this->assertContains('HTTP/1.0 405 Method Not Allowed', $response->headers);
    }

    public function testMethodNotAllowedRenders405Template(): void
    {
        $response = $this->buildResponse();
        $response->methodNotAllowed();

        $this->assertContains('Request Method Not Supported', $response->body);
    }

    public function testRedirectSendsLocationHeader(): void
    {
        $response = $this->buildResponse();
        $response->redirect('http://example.com');

        $this->assertContains('Location: http://example.com', $response->headers);
    }

    public function testRenderSendsTheRenderedTemplate(): void
    {
        $response = $this->buildResponse();
        $response->render('index.html', ['planet' => 'world']);

        $this->assertContains('Hello world!', $response->body);
    }

    public function testHeaderSendsHeaders(): void
    {
        $response = $this->buildResponse();
        $response->header('X-Frame-Options: DENY');

        $this->assertContains('X-Frame-Options: DENY', $response->headers);
    }

    private function buildResponse(): ResponseInterface
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__ . '/templates'));
        $logger = new NullLogger();

        return new TestResponse($twig, $logger);
    }
}
