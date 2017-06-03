<?php
declare(strict_types=1);

namespace Engine;

use Psr\Log\LoggerInterface;

final class Router
{
    private $routes;
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->routes = [];
        $this->logger = $logger;
    }

    public function root(string $controllerClass, string $action): void
    {
        $this->get('/', $controllerClass, $action);
    }

    public function get(string $path, string $controllerClass, string $action): void
    {
        $this->addRoute('GET', $path, $controllerClass, $action);
        $this->addRoute('HEAD', $path, $controllerClass, $action);
    }

    public function post(string $path, string $controllerClass, string $action): void
    {
        $this->addRoute('POST', $path, $controllerClass, $action);
    }

    public function addRoute(string $method, string $path, string $controllerClass, string $action): void
    {
        $this->routes[$method][$path] = [$controllerClass, $action];
    }

    public function route(Request $request, Response $response): void
    {
        $method = $request->method();
        $pathInfo = explode('?', $request->requestUri(), 2)[0];
        $this->logger->debug("Looking for matching route for {$method} {$pathInfo}");

        if (empty($this->routes[$method][$pathInfo])) {
            $this->logger->warning("Route not found for {$method} {$pathInfo}");
            $response->notFound();
            return;
        }

        list($controllerClass, $action) = $this->routes[$method][$pathInfo];
        $this->logger->debug("Route found for {$method} {$pathInfo}: {$controllerClass}#{$action}");

        $controller = new $controllerClass($request, $response, $this->logger);
        $controller->$action();
    }
}
