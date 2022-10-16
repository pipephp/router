<?php

namespace Pipe;

class Router
{
    private static $instance;
    private $routes = [];

    public const FOUND = 0;
    public const NOT_FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    //$method = $_SERVER['REQUEST_METHOD'];
    //$uri = $_SERVER['REQUEST_URI'];
    public function resolve(string $method, string $uri): object
    {
        return (object)[
            'resolution' => self::FOUND,
            'handler' => $this->routes[$method][$uri],
            'vars' => [],
        ];
    }

    public function match(string $method, string $pattern, mixed $handler): self
    {
        $this->routes[strtoupper($method)][$pattern] = $handler;

        return $this;
    }

    public function routes(): array
    {
        return $this->routes;
    }
}
