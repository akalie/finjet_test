<?php declare(strict_types=1);

/** @var $router League\Route\Router */

use Finjet\Middlewares\AuthMiddleware;
use Laminas\Diactoros\Response\JsonResponse;


$router = new League\Route\Router();

$router->map('GET', '/', fn() => new JsonResponse(['hello world!']));

$router->map('POST', '/auth', 'Finjet\Controllers\UserController::auth');

$router->group('', function ($router) {
    $router->map('GET', '/items[/{category}]', 'Finjet\Controllers\ItemsController::show');
    $router->map('POST', '/items/create', 'Finjet\Controllers\ItemsController::create');
    $router->map('POST', '/items/update', 'Finjet\Controllers\ItemsController::update');
    $router->map('POST', '/items/delete', 'Finjet\Controllers\ItemsController::delete');


    $router->map('GET', '/categories', 'Finjet\Controllers\CategoriesController::show');
    $router->map('POST', '/categories/create', 'Finjet\Controllers\CategoriesController::create');
    $router->map('POST', '/categories/update', 'Finjet\Controllers\CategoriesController::update');
    $router->map('POST', '/categories/delete', 'Finjet\Controllers\CategoriesController::delete');

})->lazyMiddleware(AuthMiddleware::class);


return $router;
