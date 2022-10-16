<?php

use Pipe\Router;

$routes = [
    "/",
    "/level1",
    "/level1/level2",
    "/level1/level2/level3",
    "/level1/level2/level3/level4",
    "/level1/level2/level3/level4/level5",
    "/level1/level2/level3/level4/level5/6thlevel",
    "/{first}",
    "/{first}/{second}",
    "/{first}/{second}/{third}",
    "/{first}/{second}/{third}/{fourth}/{fifth}/{sixth}",
    "/prefix/{first}-{first2}/another/prefix-{second}/{third}/{fourth}/{fifth}/{sixth}",
    "/prefix/{first}-{first2}/another/prefix-{second}/{third}/{fourth}/{fifth}/{sixth}/",
];

it("can route to simple routes", function () use ($routes) {
    $r = new Router();
    $r->match("GET", "/", 'simpler');
    expect($r->resolve("GET", "/")->resolution)->toEqual(Router::FOUND);
    expect($r->resolve("GET", "/")->handler)->toEqual('simpler');
});

it("can not route to simple routes with extra symbols", function () use ($routes) {
    $r = new Router();
    $r->match("GET", "/k", 'simpler');
    expect($r->resolve("GET", "/")->resolution)->toEqual(Router::NOT_FOUND);
});

it("can not route to a bad method", function () use ($routes) {
    $r = new Router();
    $r->match("GET", "/", 'simpler');
    expect($r->resolve("POST", "/")->resolution)->toEqual(Router::NOT_FOUND);
});

it("can return found params", function () use ($routes) {
    $r = new Router();
    $r->match("GET", "/{category}/{id}", 'simpler');
    expect($r->resolve("GET", "/books/432")->resolution)->toEqual(Router::FOUND);
    expect($r->resolve("GET", "/books/432")->args["category"])->toEqual("books");
    expect($r->resolve("GET", "/books/432")->args["id"])->toEqual("432");
});
