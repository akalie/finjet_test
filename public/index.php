<?php declare(strict_types=1);

include '../vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Finjet\ErrorJsonResponse;
use Laminas\Diactoros\Response;

$router = require '../config/routes.php';
$connectionConfig = require '../config/connection.php';
// unfortunately, container requires $router and $connectionConfig
$container = require '../config/container.php';
$strategy = (new League\Route\Strategy\ApplicationStrategy)->setContainer($container);
$router   = $router->setStrategy($strategy);
/** @var LoggerInterface $logger */
$logger = $container->get(LoggerInterface::class);
try {
    $request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
        $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
    );
    $sender = new Finjet\SimpleResponseSender();

    /** @var Response $response */
    $response = $router->dispatch($request);
    $sender->send($response);
} catch (League\Route\Http\Exception\MethodNotAllowedException $exception) {
    $sender->send(new ErrorJsonResponse(['No such method'], 404));
    $logger->critical($exception->getMessage());
} catch (Throwable $t) {
    $logger->critical($t->getMessage() . ' with trace ' . $t->getTraceAsString());
    $sender->send(new ErrorJsonResponse(['Something terrible happened'], 500));
}

echo 'Yey!';
