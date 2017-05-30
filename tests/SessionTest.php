<?php
declare(strict_types=1);

namespace Engine;

use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function testFetchReturnsParametersByKey(): void
    {
        $_session = ['foo' => 'bar'];
        $session = new Session($_session);

        $this->assertEquals('bar', $session->fetch('foo'));
    }

    public function testFetchReturnsEmptyStringIfParameterIsMissing(): void
    {
        $_session = ['foo' => 'bar'];
        $session = new Session($_session);

        $this->assertEquals('', $session->fetch('baz'));
    }

    public function testFetchReturnsDefaultIfParameterIsMissing(): void
    {
        $_session = ['foo' => 'bar'];
        $session = new Session($_session);

        $this->assertEquals('quux', $session->fetch('baz', 'quux'));
    }

    public function testExistReturnsTrueIfTheParameterExists(): void
    {
        $_session = ['foo' => 'bar'];
        $session = new Session($_session);

        $this->assertTrue($session->exist('foo'));
    }

    public function testExistReturnsFalseIfTheParameterIsMissing(): void
    {
        $_session = ['foo' => 'bar'];
        $session = new Session($_session);

        $this->assertFalse($session->exist('baz'));
    }

    public function testSetPopulatesTheSession(): void
    {
        $_session = ['foo' => 'bar'];
        $session = new Session($_session);
        $session->set('baz', 'quux');

        $this->assertEquals(['foo' => 'bar', 'baz' => 'quux'], $_session);
    }

    public function testDeleteRemovesTheKeyFromTheSession(): void
    {
        $_session = ['foo' => 'bar', 'baz' => 'quux'];
        $session = new Session($_session);
        $session->delete('foo');

        $this->assertEquals(['baz' => 'quux'], $_session);
    }

    public function testResetDeletesAllKeysFromTheSession(): void
    {
        $_session = ['foo' => 'bar', 'baz' => 'quux'];
        $session = new Session($_session);
        $session->reset();

        $this->assertEmpty($_session);
    }

    public function testCsrfTokenReturnsExistingCsrfTokens(): void
    {
        $_session = ['csrf_token' => 'decafbad'];
        $session = new Session($_session);

        $this->assertEquals('decafbad', $session->csrfToken());
    }

    public function testCsrfTokenGeneratesANewToken(): void
    {
        $_session = [];
        $session = new Session($_session);

        $this->assertNotEmpty($session->csrfToken());
    }

    public function testCsrfTokenStoresTheTokenInTheSession(): void
    {
        $_session = [];
        $session = new Session($_session);
        $session->csrfToken();

        $this->assertArrayHasKey('csrf_token', $_session);
    }
}
