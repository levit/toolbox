<?php

declare(strict_types=1);

/**
 * Executor Autônomo e Amigável de Testes Unitários.
 *
 * Executa todos os testes unitários da biblioteca softtan/toolbox sem depender
 * da instalação do PHPUnit no vendor, com formatação rica e código de saída compatível com CI/CD.
 */

namespace Softtan\Toolbox\Tests;

use AssertionError;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

require_once __DIR__ . '/bootstrap.php';

class TestRunner
{
    private const COLOR_RESET   = "\033[0m";
    private const COLOR_BOLD    = "\033[1m";
    private const COLOR_DIM     = "\033[2m";
    private const COLOR_RED     = "\033[31m";
    private const COLOR_GREEN   = "\033[32m";
    private const COLOR_YELLOW  = "\033[33m";
    private const COLOR_BLUE    = "\033[34m";
    private const COLOR_MAGENTA = "\033[35m";
    private const COLOR_CYAN    = "\033[36m";
    private const COLOR_WHITE   = "\033[37m";

    private int $totalFiles = 0;
    private int $totalTests = 0;
    private int $passedTests = 0;
    private int $failedTests = 0;
    private array $failures = [];
    private float $startTime = 0.0;

    public function run(): int
    {
        $this->startTime = microtime(true);
        TestCase::resetAssertionCount();

        $this->printHeader();

        $testFiles = glob(__DIR__ . '/*Test.php');
        sort($testFiles);
        $this->totalFiles = count($testFiles);

        foreach ($testFiles as $filePath) {
            $this->runTestFile($filePath);
        }

        $duration = microtime(true) - $this->startTime;
        $this->printSummary($duration);

        return $this->failedTests === 0 ? 0 : 1;
    }

    private function printHeader(): void
    {
        echo PHP_EOL;
        echo self::COLOR_BOLD . self::COLOR_CYAN . "======================================================================" . self::COLOR_RESET . PHP_EOL;
        echo self::COLOR_BOLD . self::COLOR_WHITE . "   SOFTPACK / TOOLBOX - SUÍTE DE TESTES UNITÁRIOS AUTÔNOMA           " . self::COLOR_RESET . PHP_EOL;
        echo self::COLOR_DIM . "   Namespace: Softtan\\Toolbox\\Helpers | PHP " . PHP_VERSION . self::COLOR_RESET . PHP_EOL;
        echo self::COLOR_BOLD . self::COLOR_CYAN . "======================================================================" . self::COLOR_RESET . PHP_EOL;
        echo PHP_EOL;
    }

    private function runTestFile(string $filePath): void
    {
        require_once $filePath;

        $className = 'Softtan\\Toolbox\\Tests\\' . basename($filePath, '.php');
        if (! class_exists($className)) {
            // Tenta sem namespace caso tenha sido declarado diferente
            $simpleName = basename($filePath, '.php');
            if (class_exists($simpleName)) {
                $className = $simpleName;
            } else {
                return;
            }
        }

        $refClass = new ReflectionClass($className);
        if ($refClass->isAbstract() || ! $refClass->isSubclassOf(TestCase::class)) {
            return;
        }

        $methods = $refClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $testMethods = array_filter(
            $methods,
            static fn (ReflectionMethod $m): bool => str_starts_with($m->getName(), 'test') && ! $m->isStatic()
        );

        if (empty($testMethods)) {
            return;
        }

        $shortName = basename($filePath, '.php');
        echo self::COLOR_BOLD . self::COLOR_BLUE . " • " . self::COLOR_WHITE . $shortName . self::COLOR_RESET . PHP_EOL;

        foreach ($testMethods as $method) {
            $this->runSingleTestMethod($refClass, $method->getName(), $shortName);
        }

        echo PHP_EOL;
    }

    private function runSingleTestMethod(ReflectionClass $refClass, string $methodName, string $fileLabel): void
    {
        $this->totalTests++;
        $instance = $refClass->newInstance();

        // setUp
        $setUpMethod = $refClass->getMethod('setUp');
        $setUpMethod->setAccessible(true);
        $setUpMethod->invoke($instance);

        $testPassed = false;
        $errorDetails = null;

        try {
            $instance->{$methodName}();

            // Checa se era esperada uma exceção que não foi disparada
            $expectedEx = $instance->getExpectedException();
            if ($expectedEx !== null) {
                throw new AssertionError("Falha na asserção: exceção esperada '{$expectedEx}' não foi lançada.");
            }

            $testPassed = true;
        } catch (Throwable $e) {
            $expectedEx = $instance->getExpectedException();
            if ($expectedEx !== null && $e instanceof $expectedEx) {
                $expectedMsg = $instance->getExpectedExceptionMessage();
                if ($expectedMsg === null || str_contains($e->getMessage(), $expectedMsg)) {
                    $testPassed = true;
                } else {
                    $errorDetails = sprintf(
                        "Exceção '%s' lançada, porém com mensagem diferente.\nEsperado trecho: '%s'\nRecebido: '%s'",
                        get_class($e),
                        $expectedMsg,
                        $e->getMessage()
                    );
                }
            } else {
                $errorDetails = $e->getMessage() . PHP_EOL . "   em " . $e->getFile() . ':' . $e->getLine();
            }
        } finally {
            // tearDown
            $tearDownMethod = $refClass->getMethod('tearDown');
            $tearDownMethod->setAccessible(true);
            $tearDownMethod->invoke($instance);
        }

        if ($testPassed) {
            $this->passedTests++;
            echo "   " . self::COLOR_GREEN . "✔" . self::COLOR_RESET . " " . self::COLOR_DIM . $methodName . self::COLOR_RESET . PHP_EOL;
        } else {
            $this->failedTests++;
            echo "   " . self::COLOR_RED . "✖" . self::COLOR_RESET . " " . self::COLOR_BOLD . self::COLOR_RED . $methodName . self::COLOR_RESET . PHP_EOL;
            $this->failures[] = [
                'test' => "{$fileLabel}::{$methodName}",
                'error' => $errorDetails ?? 'Falha desconhecida.',
            ];
        }
    }

    private function printSummary(float $duration): void
    {
        echo self::COLOR_CYAN . "----------------------------------------------------------------------" . self::COLOR_RESET . PHP_EOL;

        if (! empty($this->failures)) {
            echo PHP_EOL . self::COLOR_BOLD . self::COLOR_RED . "FALHAS ENCONTRADAS:" . self::COLOR_RESET . PHP_EOL;
            foreach ($this->failures as $i => $fail) {
                $num = $i + 1;
                echo self::COLOR_RED . " [{$num}] {$fail['test']}" . self::COLOR_RESET . PHP_EOL;
                echo self::COLOR_DIM . "     " . $fail['error'] . self::COLOR_RESET . PHP_EOL . PHP_EOL;
            }
            echo self::COLOR_CYAN . "----------------------------------------------------------------------" . self::COLOR_RESET . PHP_EOL;
        }

        $durationMs = number_format($duration * 1000, 2);
        $totalAssertions = TestCase::getAssertionCount();
        $memory = number_format(memory_get_peak_usage(true) / 1024 / 1024, 2);

        echo " Arquivos de Teste: " . self::COLOR_BOLD . $this->totalFiles . self::COLOR_RESET . PHP_EOL;
        echo " Total de Testes:   " . self::COLOR_BOLD . $this->totalTests . self::COLOR_RESET . PHP_EOL;
        echo " Asserções:         " . self::COLOR_BOLD . $totalAssertions . self::COLOR_RESET . PHP_EOL;
        echo " Tempo de Execução: " . self::COLOR_BOLD . "{$durationMs} ms" . self::COLOR_RESET . PHP_EOL;
        echo " Pico de Memória:   " . self::COLOR_BOLD . "{$memory} MB" . self::COLOR_RESET . PHP_EOL;
        echo PHP_EOL;

        if ($this->failedTests === 0) {
            echo self::COLOR_BOLD . self::COLOR_GREEN . " ✔ SUCESSO: Todos os {$this->totalTests} testes passaram com êxito! (100%)" . self::COLOR_RESET . PHP_EOL;
        } else {
            echo self::COLOR_BOLD . self::COLOR_RED . " ✖ FALHA: {$this->failedTests} de {$this->totalTests} testes falharam." . self::COLOR_RESET . PHP_EOL;
        }

        echo self::COLOR_CYAN . "======================================================================" . self::COLOR_RESET . PHP_EOL . PHP_EOL;
    }
}

$runner = new TestRunner();
exit($runner->run());
