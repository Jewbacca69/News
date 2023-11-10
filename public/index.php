<?php
session_start();
require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use App\Controllers\NewsController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..')->load();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute("GET", "/", [new NewsController(), "index"]);
    $r->addRoute("GET", "/country", [new NewsController(), "setCountry"]);
    $r->addRoute("GET", "/search", [new NewsController(), "search"]);
});

[$httpMethod, $uri] = [$_SERVER['REQUEST_METHOD'], rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))];
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::FOUND:
        [$controller, $method] = $routeInfo[1];
        echo $controller->{$method}($routeInfo[2]);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        break;
    default:
        echo "404. Not Found.";
}