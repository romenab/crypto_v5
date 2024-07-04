<?php

namespace CryptoTrade;

use CryptoTrade\App\RedirectResponse;
use CryptoTrade\App\Response;
use DI\Container;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function FastRoute\simpleDispatcher;

require_once 'vendor/autoload.php';
$loader = new FilesystemLoader(__DIR__ . '/Templates');
$twig = new Environment($loader);

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $routes = include('routes.php');
    foreach ($routes as $route) {
        [$method, $url, $controller] = $route;
        $r->addRoute($method, $url, $controller);
    }
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        break;
    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        [$controller, $method] = $handler;
        $response = (new $controller)->$method(...array_values($vars));
        if ($response instanceof Response) {
            echo $twig->render($response->getTemplate(), $response->getData());
        }
        if ($response instanceof RedirectResponse) {
            header('Location: ' . $response->getLocation());
        }
        break;
}