<?php

namespace Pipe;

class Router
{
    private static $instance;
    private $routes = [];

    public const FOUND = 0;
    public const NOT_FOUND = 1;

    public const RE = "~\{([a-z_][a-z0-9_-]*)\}~xi";

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function resolve(string $method, string $uri): object
    {
        $uriarr = explode("?", $uri);
        $uri = array_shift($uriarr);
        foreach ($this->routes[$method] ?? [] as $pattern => $options) {
            preg_match($pattern, $uri, $matches);
            if (count($matches)) {
                $resolution = self::FOUND;
                break;
            }
        }

        $args = [];
        foreach ($options['expected'] ?? [] as $e) {
            $args[$e] = $matches[$e];
        }

        return (object) [
            'resolution' => $resolution ?? self::NOT_FOUND,
            'handler' => ($resolution ?? self::NOT_FOUND) == self::FOUND ? $options["handler"] : "404",
            'args' => $args,
        ];
    }

    private function regexize(string $pattern): array
    {
        $segments = preg_split(self::RE, $pattern);
        preg_match_all(self::RE, $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        $re = [];
        $nameds = [];
        $offset = 0;

        $count = count($matches);
        for ($i = 0; $i < $count; $i++) {
            $set = $matches[$i];

            $re[] = substr($pattern, $offset, $set[0][1] - $offset);

            // catch anything that's between regex and next char.
            // unless at the end. then catch... anything?
            $named = substr($set[0][0], 1, -1);
            $nameds[] = $named;

            $tre = "(?<$named>";
            $tre .= $segments[$i + 1] ? "[^" . $segments[$i + 1][0] . "]+" : ".+";
            $tre .= ")";
            $re[] = $tre;

            $offset = $set[0][1] + strlen($set[0][0]);
        }

        if ($offset !== strlen($pattern)) {
            $re[] = substr($pattern, $offset);
        }

        return [
            "regex" => "~^" . implode("", $re) . "$~",
            "expected" => $nameds,
        ];
    }

    public function match(string $method, string $pattern, mixed $handler): self
    {
        $rex = $this->regexize($pattern);
        $this->routes[strtoupper($method)][$rex["regex"]] = [
            "handler" => $handler,
            "expected" => $rex["expected"],
        ];

        return $this;
    }

    public function routes(): array
    {
        return $this->routes;
    }
}
