<?php

declare(strict_types=1);

/**
 * Bootstrap de testes para a biblioteca softtan/toolbox.
 *
 * Configura autoloader e emula serviços do ecossistema Laravel (Session, Request, App, Auth, Response)
 * permitindo que a suíte de testes unitários execute de forma rápida, isolada e sem dependências externas.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';

// In-Memory Session Store
class TestSessionStore
{
    private static ?TestSessionStore $instance = null;
    private array $data = [];
    private array $flashed = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::getInstance()->data = [];
        self::getInstance()->flashed = [];
    }

    public function put(string|array $key, mixed $value = null): void
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setNested($this->data, $k, $v);
            }
        } else {
            $this->setNested($this->data, $key, $value);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $val = $this->getNested($this->data, $key);
        if ($val === null) {
            $val = $this->getNested($this->flashed, $key);
        }

        if ($val === null) {
            return is_callable($default) ? $default() : $default;
        }

        return $val;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function forget(string|array $keys): void
    {
        foreach ((array) $keys as $k) {
            $this->forgetNested($this->data, $k);
            $this->forgetNested($this->flashed, $k);
        }
    }

    public function flash(string $key, mixed $value): void
    {
        $this->setNested($this->flashed, $key, $value);
    }

    public function all(): array
    {
        return array_merge($this->data, $this->flashed);
    }

    private function setNested(array &$target, string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $cur = &$target;
        while (count($segments) > 1) {
            $seg = array_shift($segments);
            if (! isset($cur[$seg]) || ! is_array($cur[$seg])) {
                $cur[$seg] = [];
            }
            $cur = &$cur[$seg];
        }
        $cur[array_shift($segments)] = $value;
    }

    private function getNested(array $target, string $key): mixed
    {
        $segments = explode('.', $key);
        foreach ($segments as $segment) {
            if (is_array($target) && array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } else {
                return null;
            }
        }

        return $target;
    }

    private function forgetNested(array &$target, string $key): void
    {
        $segments = explode('.', $key);
        $cur = &$target;
        while (count($segments) > 1) {
            $seg = array_shift($segments);
            if (! isset($cur[$seg]) || ! is_array($cur[$seg])) {
                return;
            }
            $cur = &$cur[$seg];
        }
        unset($cur[array_shift($segments)]);
    }
}

// In-Memory Request & Route
class TestRoute
{
    public function __construct(
        private ?string $name = null,
        private array $parameters = []
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function parameter(string $name, mixed $default = null): mixed
    {
        return $this->parameters[$name] ?? $default;
    }
}

class TestRequestStore
{
    private static ?TestRequestStore $instance = null;
    private ?TestRoute $route = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::getInstance()->route = null;
    }

    public static function setRoute(?string $name, array $params = []): void
    {
        self::getInstance()->route = $name !== null ? new TestRoute($name, $params) : null;
    }

    public function route(): ?TestRoute
    {
        return $this->route;
    }
}

// In-Memory Application
class TestAppStore
{
    private static ?TestAppStore $instance = null;
    private string $environment = 'testing';
    private array $bindings = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::getInstance()->environment = 'testing';
        self::getInstance()->bindings = [];
    }

    public function setEnvironment(string $env): void
    {
        $this->environment = $env;
    }

    public function environment(...$environments): bool|string
    {
        if (empty($environments)) {
            return $this->environment;
        }

        return in_array($this->environment, $environments, true);
    }

    public function bound(string $abstract): bool
    {
        if ($abstract === 'session') {
            return true;
        }

        return isset($this->bindings[$abstract]);
    }

    public function bind(string $abstract, mixed $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function make(string $abstract): mixed
    {
        if (isset($this->bindings[$abstract])) {
            return is_callable($this->bindings[$abstract]) ? ($this->bindings[$abstract])() : $this->bindings[$abstract];
        }

        return null;
    }
}

// In-Memory JsonResponse and ResponseFactory
class TestJsonResponse
{
    public function __construct(
        private mixed $data = null,
        private int $status = 200,
        private array $headers = []
    ) {}

    public function getData(bool $assoc = true): mixed
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}

class TestResponseFactory
{
    public function json(mixed $data = null, int $status = 200, array $headers = []): TestJsonResponse
    {
        return new TestJsonResponse($data, $status, $headers);
    }
}

// In-Memory Auth
class TestAuth
{
    private static mixed $user = null;

    public static function user(): mixed
    {
        return self::$user;
    }

    public static function check(): bool
    {
        return self::$user !== null;
    }

    public static function id(): mixed
    {
        return self::$user?->id ?? null;
    }

    public static function setUser(mixed $user): void
    {
        self::$user = $user;
    }

    public static function reset(): void
    {
        self::$user = null;
    }
}

// Global functions for emulating Laravel helpers if not defined
if (! function_exists('session')) {
    function session(mixed $key = null, mixed $default = null): mixed
    {
        $store = TestSessionStore::getInstance();

        if ($key === null) {
            return $store;
        }

        if (is_array($key)) {
            $store->put($key);

            return null;
        }

        return $store->get($key, $default);
    }
}

if (! function_exists('request')) {
    function request(): TestRequestStore
    {
        return TestRequestStore::getInstance();
    }
}

if (! function_exists('app')) {
    function app(?string $abstract = null): mixed
    {
        $app = TestAppStore::getInstance();

        if ($abstract === null) {
            return $app;
        }

        return $app->make($abstract);
    }
}

if (! function_exists('response')) {
    function response(): TestResponseFactory
    {
        return new TestResponseFactory();
    }
}

// Register Auth alias for Illuminate\Support\Facades\Auth
if (! class_exists('Illuminate\Support\Facades\Auth')) {
    class_alias(TestAuth::class, 'Illuminate\Support\Facades\Auth');
}
