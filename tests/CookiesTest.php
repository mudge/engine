<?php
declare(strict_types=1);

namespace Engine;

use PHPUnit\Framework\TestCase;

final class CookiesTest extends TestCase
{
    public function testFetchReturnsParametersByKey(): void
    {
        $_cookies = ['foo' => 'bar'];
        $cookies = new Cookies($_cookies);

        $this->assertEquals('bar', $cookies->fetch('foo'));
    }

    public function testFetchReturnsEmptyStringIfParameterIsMissing(): void
    {
        $_cookies = ['foo' => 'bar'];
        $cookies = new Cookies($_cookies);

        $this->assertEquals('', $cookies->fetch('baz'));
    }

    public function testFetchReturnsDefaultIfParameterIsMissing(): void
    {
        $_cookies = ['foo' => 'bar'];
        $cookies = new Cookies($_cookies);

        $this->assertEquals('quux', $cookies->fetch('baz', 'quux'));
    }

    public function testExistReturnsTrueIfTheParameterExists(): void
    {
        $_cookies = ['foo' => 'bar'];
        $cookies = new Cookies($_cookies);

        $this->assertTrue($cookies->exist('foo'));
    }

    public function testExistReturnsFalseIfTheParameterIsMissing(): void
    {
        $_cookies = ['foo' => 'bar'];
        $cookies = new Cookies($_cookies);

        $this->assertFalse($cookies->exist('baz'));
    }
}
