<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use AssertionError;

/**
 * Trait com métodos utilitários de asserções comparativas e lógicas para testes.
 */
trait AssertsTrait
{
    protected function assertTrue(mixed $condition, string $message = ''): void
    {
        self::$assertionCount++;
        if ($condition !== true) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado true, recebido '.var_export($condition, true);
            throw new AssertionError($msg);
        }
    }

    protected function assertFalse(mixed $condition, string $message = ''): void
    {
        self::$assertionCount++;
        if ($condition !== false) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado false, recebido '.var_export($condition, true);
            throw new AssertionError($msg);
        }
    }

    protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($expected !== $actual) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção assertSame:\nEsperado: %s\nRecebido: %s",
                var_export($expected, true),
                var_export($actual, true)
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertNotSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($expected === $actual) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção assertNotSame: valores são idênticos (%s)",
                var_export($actual, true)
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($expected != $actual) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção assertEquals:\nEsperado: %s\nRecebido: %s",
                var_export($expected, true),
                var_export($actual, true)
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertNotEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($expected == $actual) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção assertNotEquals: valores são equivalentes (%s)",
                var_export($actual, true)
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertNull(mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($actual !== null) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado null, recebido '.var_export($actual, true);
            throw new AssertionError($msg);
        }
    }

    protected function assertNotNull(mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($actual === null) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado valor não nulo, recebido null';
            throw new AssertionError($msg);
        }
    }

    protected function assertCount(int $expectedCount, mixed $countable, string $message = ''): void
    {
        self::$assertionCount++;
        $actualCount = is_countable($countable) ? count($countable) : 0;
        if ($actualCount !== $expectedCount) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção assertCount: esperado %d elementos, recebido %d",
                $expectedCount,
                $actualCount
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
    {
        self::$assertionCount++;
        if (! str_contains($haystack, $needle)) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção: string '%s' não contém o trecho '%s'",
                $haystack,
                $needle
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertStringNotContainsString(string $needle, string $haystack, string $message = ''): void
    {
        self::$assertionCount++;
        if (str_contains($haystack, $needle)) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção: string '%s' contém indevidamente o trecho '%s'",
                $haystack,
                $needle
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        self::$assertionCount++;
        if (preg_match($pattern, $string) !== 1) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção: string '%s' não confere com o padrão regex '%s'",
                $string,
                $pattern
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertArrayHasKey(string|int $key, array $array, string $message = ''): void
    {
        self::$assertionCount++;
        if (! array_key_exists($key, $array)) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção: chave '%s' não encontrada no array",
                (string) $key
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertArrayNotHasKey(string|int $key, array $array, string $message = ''): void
    {
        self::$assertionCount++;
        if (array_key_exists($key, $array)) {
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção: chave '%s' encontrada indevidamente no array",
                (string) $key
            );
            throw new AssertionError($msg);
        }
    }

    protected function assertInstanceOf(string $expectedClass, mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if (! ($actual instanceof $expectedClass)) {
            $actualType = is_object($actual) ? get_class($actual) : gettype($actual);
            $msg = $message !== '' ? $message : sprintf(
                "Falha na asserção: esperado instância de '%s', recebido '%s'",
                $expectedClass,
                $actualType
            );
            throw new AssertionError($msg);
        }
    }
}
