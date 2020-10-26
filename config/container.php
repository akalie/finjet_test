<?php declare(strict_types=1);

use Finjet\Repositories\CategoriesRepository;
use Finjet\Repositories\CategoriesRepositoryInterface;
use Finjet\Repositories\ItemsRepository;
use Finjet\Repositories\ItemsRepositoryInterface;
use Finjet\Repositories\UsersRepositoryInterface;
use Finjet\Repositories\UsersRepository;
use Finjet\SimpleNullLogger;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Log\LoggerInterface;

$container = new League\Container\Container;
$container->delegate(
    new League\Container\ReflectionContainer
);

/**
 * @var array $connectionConfig provided in index.php
 */
$container->add('connection', function() use ($connectionConfig) {
    $capsule = new Capsule;

    $capsule->addConnection([
        'driver' => $connectionConfig['db_driver'],
        'host' => $connectionConfig['db_host'],
        'database' => $connectionConfig['db_name'],
        'username' => $connectionConfig['db_user'],
        'password' => $connectionConfig['db_pass']
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
})->setShared(true);

$container->add(LoggerInterface::class, SimpleNullLogger::class);

$container->add(
    UsersRepositoryInterface::class,
    new UsersRepository($container->get('connection')),
    true
);

$container->add(
    ItemsRepositoryInterface::class,
    new ItemsRepository($container->get('connection')),
    true
);
$container->add(
    CategoriesRepositoryInterface::class,
    new CategoriesRepository($container->get('connection')),
    true
);
return $container;