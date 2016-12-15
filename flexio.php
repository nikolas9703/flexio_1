<?php

require_once 'vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;

$app = new Slim\App();
$container = $app->getContainer();
// This is needed for file based session driver
$container['files'] = function () {
    return new Illuminate\Filesystem\Filesystem();
};

$container['config'] = new Illuminate\Config\Repository();

$container['config']['session.lifetime'] = 120; // Minutes idleable
$container['config']['session.expire_on_close'] = false;
$container['config']['session.lottery'] = array(2, 100); // lottery--how often do they sweep storage location to clear old ones?
$container['config']['session.cookie'] = 'laravel_session';
$container['config']['session.path'] = '/';
$container['config']['session.domain'] = null;
$container['config']['session.driver'] = 'file';
$container['config']['session.files'] = __DIR__ . '/application/cache/';

$container->register(new Flexio\Provider\SessionProvider);
$app->run();
