<?php
declare(strict_types=1);

namespace Engine;

use PHPUnit\Framework\TestCase;

final class ParametersTest extends TestCase
{
    public function testFetchReturnsParametersByKey(): void
    {
        $params = new Parameters(['foo' => 'bar']);

        $this->assertEquals('bar', $params->fetch('foo'));
    }

    public function testFetchReturnsEmptyStringIfParameterIsMissing(): void
    {
        $params = new Parameters(['foo' => 'bar']);

        $this->assertEquals('', $params->fetch('baz'));
    }

    public function testFetchReturnsDefaultIfParameterIsMissing(): void
    {
        $params = new Parameters(['foo' => 'bar']);

        $this->assertEquals('quux', $params->fetch('baz', 'quux'));
    }

    public function testExistReturnsTrueIfTheParameterExists(): void
    {
        $params = new Parameters(['foo' => 'bar']);

        $this->assertTrue($params->exist('foo'));
    }

    public function testExistReturnsFalseIfTheParameterIsMissing(): void
    {
        $params = new Parameters(['foo' => 'bar']);

        $this->assertFalse($params->exist('baz'));
    }
}
