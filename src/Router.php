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
        $pathInfo = $request->pathInfo();
        $this->logger->debug("Looking for matching route for {$method} {$pathInfo}");

        foreach ($this->routes[$method] as $path => list($controllerClass, $action)) {
            if ($pathInfo === $path) {
                $this->logger->debug("Route {$path} found for {$method} {$pathInfo}: {$controllerClass}#{$action}");
                $controller = new $controllerClass($request, $response, $this->logger);
                $controller->$action();

                return;
            }
        }

        $response->notFound();
    }
}
