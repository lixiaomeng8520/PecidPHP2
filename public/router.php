<?php
use \Psr\Http\Message\ServerRequestInterface;

use \Zend\Diactoros\ServerRequestFactory;
use \Zend\Diactoros\Response;

use \Aura\Router\RouterContainer;

require __DIR__ . '/vendor/autoload.php';

$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('blog.read', '/blog/{id}', function (ServerRequestInterface $request) {
    $id = $request->getAttribute('id');
    var_dump($id);
    $response = new Response();
    $response->getBody()->write("You asked for blog entry ${id}");
    return $response;
})->defaults([
    'id' => 2
]);

$matcher = $routerContainer->getMatcher();

// print_r($matcher);

$route = $matcher->match($request);
if (!$route) {
    echo "No route found for the request";
    exit;
}

foreach ($route->attributes as $k => $v) {
    $request = $request->withAttribute($k, $v);
}

$callable = $route->handler;
$response = $callable($request);

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

http_response_code($response->getStatusCode());
echo $response->getBody();
