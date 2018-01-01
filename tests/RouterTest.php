<?php
declare(strict_types=1);

namespace Engine;

use PHPUnit\Framework\TestCase;

final class TestController extends Controller
{
    public function index(): void
    {
        $this->render('index.html', ['planet' => 'Mars']);
    }

    public function halt(): void
    {
        $this->header('Location: http://example.com');
        throw new HaltingResponseException('Stop responding');
        $this->header('Location: http://example.com/unreachable');
    }

    public function error(): void
    {
        throw new \RuntimeException('Something went wrong');
    }
}

final class RouterTest extends TestCase
{
    public function testRootSetsUpRouteForHomepage(): void
    {
        $request = $this->buildRequest('GET', '/');
        $response = $this->buildResponse();
        $router = $this->buildRouter();

        $router->root('Engine\TestController', 'index');
        $router->route($request, $response);

        $this->assertContains('Hello Mars!', $response->body);
    }

    public function testGetSetsUpRouteForGetRequests(): void
    {
        $request = $this->buildRequest('GET', '/foo');
        $response = $this->buildResponse();
        $router = $this->buildRouter();

        $router->get('/foo', 'Engine\TestController', 'index');
        $router->route($request, $response);

        $this->assertContains('Hello Mars!', $response->body);
    }

    public function testGetSetsUpRouteForHeadRequests(): void
    {
        $request = $this->buildRequest('HEAD', '/foo');
        $response = $this->buildResponse();
        $router = $this->buildRouter();

        $router->get('/foo', 'Engine\TestController', 'index');
        $router->route($request, $response);

        $this->assertContains('Hello Mars!', $response->body);
    }

    public function testPostSetsUpRouteForPostRequests(): void
    {
        $request = $this->buildRequest('POST', '/bar');
        $response = $this->buildResponse();
        $router = $this->buildRouter();

        $router->post('/bar', 'Engine\TestController', 'index');
        $router->route($request, $response);

        $this->assertContains('Hello Mars!', $response->body);
    }

    public function testAddRouteSetsUpRouteForMethodAndPath(): void
    {
        $request = $this->buildRequest('PUT', '/baz');
        $response = $this->buildResponse();
        $router = $this->buildRouter();

        $router->addRoute('PUT', '/baz', 'Engine\TestController', 'index');
        $router->route($request, $response);

        $this->assertContains('Hello Mars!', $response->body);
    }

    public function testRouteReturns404ForMissingRoutes(): void
    {
        $request = $this->buildRequest('GET', '/quux');
        $response = $this->buildResponse();
        $router = $this->buildRouter();

        $router->route($request, $response);

        $this->assertContains('HTTP/1.0 404 Not Found', $response->headers);
    }

    public function testRouteGracefullyHandlesHaltingResponseExceptions(): void
    {
        $request = $this->buildRequest('GET', '/quuz');
        $response = $this->buildResponse();
        $router = $this->buildRouter();

        $router->get('/quuz', 'Engine\TestController', 'halt');
        $router->route($request, $response);

        $this->assertNotContains('Location: http://example.com/unreachable', $response->headers);
    }

    public function testRouteRaisesOtherExceptions(): void
    {
        $request = $this->buildRequest('GET', '/bad');
        $response = $this->buildResponse();
        $router = $this->buildRouter();
        $router->get('/bad', 'Engine\TestController', 'error');

        $this->expectException(\RuntimeException::class);

        $router->route($request, $response);
    }

    public function testRouteOnlyUsesTheLatestRouteDefinition(): void
    {
        $request = $this->buildRequest('GET', '/');
        $response = $this->buildResponse();
        $router = $this->buildRouter();

        $router->root('Engine\TestController', 'halt');
        $router->root('Engine\TestController', 'index');
        $router->route($request, $response);

        $this->assertContains('Hello Mars!', $response->body);
    }

    private function buildRouter(): Router
    {
        return new Router();
    }

    private function buildRequest(string $method, string $uri): RequestInterface
    {
        return new TestRequest($method, $uri);
    }

    private function buildResponse(): ResponseInterface
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__ . '/templates'));

        return new TestResponse($twig);
    }
}
