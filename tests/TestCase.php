<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

require_once __DIR__ . '/AssertsTrait.php';
require_once __DIR__ . '/TypeAssertsTrait.php';

/**
 * Classe base para os testes unitários da biblioteca softtan/toolbox.
 *
 * Fornece compatibilidade transparente tanto com o executor autônomo (tests/run.php)
 * quanto com o PHPUnit oficial (vendor/bin/phpunit).
 */
abstract class TestCase
{
    use AssertsTrait;
    use TypeAssertsTrait;

    protected static int $assertionCount = 0;
    private ?string $expectedException = null;
    private ?string $expectedExceptionMessage = null;

    public static function getAssertionCount(): int
    {
        return self::$assertionCount;
    }

    public static function resetAssertionCount(): void
    {
        self::$assertionCount = 0;
    }

    protected function resetEnvironment(): void
    {
        \TestSessionStore::reset();
        \TestRequestStore::reset();
        \TestAppStore::reset();
        \TestAuth::reset();
    }

    protected function setUp(): void
    {
        $this->resetEnvironment();
    }

    protected function tearDown(): void
    {
        $this->clearExpectedException();
        $this->resetEnvironment();
    }

    public function expectException(string $exceptionClass): void
    {
        $this->expectedException = $exceptionClass;
    }

    public function expectExceptionMessage(string $message): void
    {
        $this->expectedExceptionMessage = $message;
    }

    public function getExpectedException(): ?string
    {
        return $this->expectedException;
    }

    public function getExpectedExceptionMessage(): ?string
    {
        return $this->expectedExceptionMessage;
    }

    public function clearExpectedException(): void
    {
        $this->expectedException = null;
        $this->expectedExceptionMessage = null;
    }
}
