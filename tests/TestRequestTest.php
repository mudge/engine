<?php
declare(strict_types=1);

namespace Engine;

use PHPUnit\Framework\TestCase;

final class TestRequestTest extends TestCase
{
    public function testMethodReturnsMethod(): void
    {
        $request = new TestRequest('GET', '/');

        $this->assertEquals('GET', $request->method());
    }

    public function testRequestUriReturnsUri(): void
    {
        $request = new TestRequest('GET', '/');

        $this->assertEquals('/', $request->requestUri());
    }

    public function testRequestPathReturnsPathWithoutQuery(): void
    {
        $request = new TestRequest('GET', '/index?foo=bar');

        $this->assertEquals('/index', $request->requestPath());
    }

    public function testAcceptsGetParameters(): void
    {
        $request = new TestRequest('GET', '/', ['foo' => 'bar']);

        $this->assertEquals('bar', $request->params()->fetch('foo'));
    }

    public function testAcceptsPostParameters(): void
    {
        $request = new TestRequest('GET', '/', [], ['foo' => 'bar']);

        $this->assertEquals('bar', $request->params()->fetch('foo'));
    }

    public function testAcceptsCookies(): void
    {
        $request = new TestRequest('GET', '/', [], [], ['foo' => 'bar']);

        $this->assertEquals('bar', $request->cookies()->fetch('foo'));
    }

    public function testAcceptsSession(): void
    {
        $request = new TestRequest('GET', '/', [], [], [], ['foo' => 'bar']);

        $this->assertEquals('bar', $request->session()->fetch('foo'));
    }
}
