<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use AssertionError;

/**
 * Trait com métodos utilitários de asserção de tipos primitivos para testes.
 */
trait TypeAssertsTrait
{
    protected function assertIsArray(mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if (! is_array($actual)) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado array, recebido '.gettype($actual);
            throw new AssertionError($msg);
        }
    }

    protected function assertIsString(mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if (! is_string($actual)) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado string, recebido '.gettype($actual);
            throw new AssertionError($msg);
        }
    }

    protected function assertIsInt(mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if (! is_int($actual)) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado int, recebido '.gettype($actual);
            throw new AssertionError($msg);
        }
    }

    protected function assertIsFloat(mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if (! is_float($actual)) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado float, recebido '.gettype($actual);
            throw new AssertionError($msg);
        }
    }

    protected function assertIsBool(mixed $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if (! is_bool($actual)) {
            $msg = $message !== '' ? $message : 'Falha na asserção: esperado bool, recebido '.gettype($actual);
            throw new AssertionError($msg);
        }
    }
}
