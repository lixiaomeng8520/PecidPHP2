<?php
ini_set('display_errors', 'On');

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Server\RequestHandlerInterface;

use \Zend\Diactoros\Response;
use \Zend\Diactoros\ServerRequestFactory;

use Relay\Relay;

require dirname(__DIR__) . '/vendor/autoload.php';

$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$queue = [];
$queue[] = function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
    $response = $handler->handle($request);
    $response->getBody()->write("my middleware1");
    return $response;
};

$queue[] = function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
    $response = new Response();
    $response->getBody()->write("my middleware2");
    return $response;
};

$relay = new Relay($queue);

$response = $relay->handle($request);

echo $response->getBody();