<?php
declare(strict_types=1);

namespace Engine;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class ControllerTest extends TestCase
{
    public function testRenderFormPassesCsrfTokenToTemplate(): void
    {
        $request = $this->buildRequest(['csrf_token' => 'decafbad']);
        $response = $this->buildResponse();
        $logger = new NullLogger();
        $controller = new class($request, $response, $logger) extends Controller {
            public function index(): void
            {
                $this->renderForm('form.html');
            }
        };
        $controller->index();

        $this->assertContains('CSRF token is decafbad', $response->body);
    }

    public function testRenderRendersTheResponse(): void
    {
        $request = $this->buildRequest();
        $response = $this->buildResponse();
        $logger = new NullLogger();
        $controller = new class($request, $response, $logger) extends Controller {
            public function index(): void
            {
                $this->render('index.html', ['planet' => 'Ilus']);
            }
        };
        $controller->index();

        $this->assertContains('Hello Ilus!', $response->body);
    }

    public function testRedirectSendsLocationHeader(): void
    {
        $request = $this->buildRequest();
        $response = $this->buildResponse();
        $logger = new NullLogger();
        $controller = new class($request, $response, $logger) extends Controller {
            public function index(): void
            {
                $this->redirect('http://example.com');
            }
        };
        $controller->index();

        $this->assertContains('Location: http://example.com', $response->headers);
    }

    public function testHeaderSendsHeader(): void
    {
        $request = $this->buildRequest();
        $response = $this->buildResponse();
        $logger = new NullLogger();
        $controller = new class($request, $response, $logger) extends Controller {
            public function index(): void
            {
                $this->header('HTTP/1.0 418 I\'m a teapot');
            }
        };
        $controller->index();

        $this->assertContains('HTTP/1.0 418 I\'m a teapot', $response->headers);
    }

    public function testCsrfTokenFetchesTokenFromSession(): void
    {
        $request = $this->buildRequest(['csrf_token' => 'decafbad']);
        $response = $this->buildResponse();
        $logger = new NullLogger();
        $controller = new class($request, $response, $logger) extends Controller {};

        $this->assertEquals('decafbad', $controller->csrfToken());
    }

    public function testVerifyCsrfTokenDoesNothingWithValidToken(): void
    {
        $request = $this->buildRequest(['csrf_token' => 'decafbad'], ['csrf_token' => 'decafbad']);
        $response = $this->buildResponse();
        $logger = new NullLogger();
        $controller = new class($request, $response, $logger) extends Controller {
            public function index(): void
            {
                $this->verifyCsrfToken();

                $this->render('index.html', ['planet' => 'Ilus']);
            }
        };
        $controller->index();

        $this->assertContains('Hello Ilus!', $response->body);
    }

    public function testVerifyCsrfTokenThrowsHaltingResponseException(): void
    {
        $request = $this->buildRequest(['csrf_token' => 'baaaaaad'], ['csrf_token' => 'decafbad']);
        $response = $this->buildResponse();
        $logger = new NullLogger();
        $controller = new class($request, $response, $logger) extends Controller {
            public function index(): void
            {
                $this->verifyCsrfToken();

                $this->render('index.html', ['planet' => 'Ilus']);
            }
        };

        $this->expectException(HaltingResponseException::class);
        $controller->index();
    }

    private function buildRequest($session = [], $post = []): RequestInterface
    {
        return new TestRequest('GET', '/new', [], $post, [], $session);
    }

    private function buildResponse(): ResponseInterface
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__ . '/templates'));
        $logger = new NullLogger();

        return new TestResponse($twig, $logger);
    }
}
