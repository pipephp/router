<?php

use Pipe\Router;

use function Pipe\route;

it("singleton calls a route function", function () {
    expect(route())->toBeInstanceOf(Router::class);
});

it("singleton adds get route with string action", function () {
    $r = route()->match("GET", "/", "home");
    $routes = $r->routes();
    expect($routes)->toMatchArray(["GET" => ["/" => "home"]]);
});

it("singleton overwrites doubled matches", function () {
    $r = route()->match("GET", "/", "home");
    $r = route()->match("GET", "/", "redirect");
    $routes = $r->routes();
    expect($routes)->toMatchArray(["GET" => ["/" => "redirect"]]);
    expect($routes)->toHaveCount(1);
});

it("can be made with callable", function () {
    $r = route()->match("GET", "/", function () {
    });
    expect($r->resolve("GET", "/")->handler)->toBeCallable();
});

it("can be instanciated multiple times", function () {
    $r1 = (new Router())->match("GET", "/", "home");
    $r2 = (new Router())->match("GET", "/", "about");

    expect($r1->routes())->not->toMatchArray($r2->routes());
});

it("can resolve uri's", function () {
    route()->match("GET", "/url", "home");
    $r = route()->resolve("GET", "/url");

    expect($r->resolution)->toEqual(Router::FOUND);
    expect($r->handler)->toEqual("home");
});
