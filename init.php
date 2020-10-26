<?php declare(strict_types=1);

include './vendor/autoload.php';


use Finjet\Entities\Category;
use Finjet\Entities\Item;
use Illuminate\Database\Capsule\Manager as Capsule;

$pdoConfig = require './config/connection.php';
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => $pdoConfig['db_driver'],
    'host' => $pdoConfig['db_host'],
    'database' => $pdoConfig['db_name'],
    'username' => $pdoConfig['db_user'],
    'password' => $pdoConfig['db_pass']
]);
$capsule->bootEloquent();

// create tables
$capsule->getDatabaseManager()->statement('
    CREATE TABLE IF NOT EXISTS users
    (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        login       VARCHAR(255) NOT NULL,
        pass_hash   CHAR(60) NOT NULL,
        token       CHAR(36),
        token_expires_at  TIMESTAMP,
        UNIQUE INDEX (token) USING HASH
    ) 
');
$capsule->getDatabaseManager()->statement('
    CREATE TABLE IF NOT EXISTS items
    (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(255) NOT NULL
    )
');
$capsule->getDatabaseManager()->statement('
    CREATE TABLE IF NOT EXISTS categories
    (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(255) NOT NULL,
        UNIQUE INDEX (name)
    )
');
$capsule->getDatabaseManager()->statement('
    CREATE TABLE IF NOT EXISTS category_to_item
    (
        category_id INT NOT NULL,
        item_id INT NOT NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE 
    )
');

// add user
$passHash = password_hash('test', PASSWORD_DEFAULT, ['salt' => 'dsfasfasdfasdfasfasdfasdfaf']);
$capsule->getDatabaseManager()->insert("insert into users values (null, 'askaslie', '$passHash', null, null)");

// create some categories and items
if (Category::whereName('catA')->count() == 0) {
    $categoryA = new Category(['name' => 'catA']);
    $categoryA->save();
    $categoryB = new Category(['name' => 'catB']);
    $categoryB->save();


    for ($i = 0; $i < 10; $i++) {
        $item = new Item(['name' => 'item' . ($i + 1)]);
        $category = random_int(1, 2) === 1 ? $categoryA : $categoryB;
        $item->save();
        $item->categories()->save($category);
    }
}

