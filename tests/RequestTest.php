<?php
declare(strict_types=1);

namespace Engine;

use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testMethodReturnsRequestMethod(): void
    {
        $request = $this->request(['server' => ['REQUEST_METHOD' => 'GET']]);

        $this->assertEquals('GET', $request->method());
    }

    public function testParamsReturnsGetVariables(): void
    {
        $request = $this->request(['get' => ['foo' => 'bar']]);
        $params = $request->params();

        $this->assertEquals('bar', $params->fetch('foo'));
    }

    public function testParamsReturnsPostVariables(): void
    {
        $request = $this->request(['post' => ['foo' => 'bar']]);
        $params = $request->params();

        $this->assertEquals('bar', $params->fetch('foo'));
    }

    public function testParamsReturnsBothGetAndPostVariables(): void
    {
        $request = $this->request(['get' => ['foo' => 'bar'], 'post' => ['baz' => 'quux']]);
        $params = $request->params();

        $this->assertEquals('bar', $params->fetch('foo'));
        $this->assertEquals('quux', $params->fetch('baz'));
    }

    public function testCookiesReturnsCookieVariables(): void
    {
        $request = $this->request(['cookies' => ['user_id' => '123']]);
        $cookies = $request->cookies();

        $this->assertEquals(123, $cookies->fetch('user_id'));
    }

    public function testRequestUriReturnsUri(): void
    {
        $request = $this->request(['server' => ['REQUEST_URI' => '/index.html']]);

        $this->assertEquals('/index.html', $request->requestUri());
    }

    public function testRequestPathReturnsRequestUri(): void
    {
        $request = $this->request(['server' => ['REQUEST_URI' => '/foobar']]);

        $this->assertEquals('/foobar', $request->requestPath());
    }

    public function testRequestPathReturnsPathWithoutQueryString(): void
    {
        $request = $this->request(['server' => ['REQUEST_URI' => '/foobar?baz=quux']]);

        $this->assertEquals('/foobar', $request->requestPath());
    }

    public function testSessionReturnsSessionVariables(): void
    {
        $request = $this->request(['session' => ['user' => 'alice']]);
        $session = $request->session();

        $this->assertEquals('alice', $session->fetch('user'));
    }

    public function testEditingSessionEditsOriginal(): void
    {
        $session = [];
        $request = $this->request(['session' => &$session]);
        $request->session()->set('foo', 'bar');

        $this->assertEquals(['foo' => 'bar'], $session);
    }

    private function request(array $arguments = []): Request
    {
        $get = $arguments['get'] ?? [];
        $post = $arguments['post'] ?? [];
        $server = $arguments['server'] ?? [];

        if (isset($arguments['cookies'])) {
            $cookies =& $arguments['cookies'];
        } else {
            $cookies = [];
        }

        if (isset($arguments['session'])) {
            $session =& $arguments['session'];
        } else {
            $session = [];
        }

        return new Request($get, $post, $cookies, $session, $server);
    }
}
