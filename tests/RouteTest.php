<?php

use Pipe\Router;

use function Pipe\route;

it("singleton calls a route function", function () {
    expect(route())->toBeInstanceOf(Router::class);
});

it("singleton adds get route with string action", function () {
    $r = (new Router())->match("GET", "/", "home");
    $routes = $r->routes();
    expect($routes)->toMatchArray([
        "GET" => [
            "~^/$~" => [
                "handler" => "home",
                "expected" => [],
            ],
        ],
    ]);
});

it("singleton overwrites doubled matches", function () {
    $r = route()->match("GET", "/", "home");
    $r = route()->match("GET", "/", "redirect");
    $routes = $r->routes();
    expect($routes)->toMatchArray([
        "GET" => [
            "~^/$~" => [
                "handler" => "redirect",
                "expected" => [],
            ],
        ],
    ]);
    expect($routes)->toHaveCount(1);
});

it("can be made with callable", function () {
    $r = (new Router())->match("GET", "/", function () {
    });
    expect($r->resolve("GET", "/")->handler)->toBeCallable();
});

it("can be instanciated multiple times", function () {
    $r1 = (new Router())->match("GET", "/", "home");
    $r2 = (new Router())->match("GET", "/", "about");

    expect($r1->routes())->not->toMatchArray($r2->routes());
});

it("matches with or without trailin slash", function () {
    $r = (new Router())
    ->match("GET", "/about", "about-noslash");

    expect($r->resolve("GET", "/about")->resolution)->toEqual(0);
    expect($r->resolve("GET", "/about")->handler)->toEqual("about-noslash");
    expect($r->resolve("GET", "/about/")->handler)->toEqual("about");
});
