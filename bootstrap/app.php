<?php
use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\Cookie;
require_once 'vendor/autoload.php';


$app = new \Slim\App();
// Init the container
$container = new Container;
$container->bind('app', $container);
$container['config'] = new Config(require 'config/session.php');
$container['files'] = new Filesystem;


$container['config']['session.lottery'] = [2, 100]; // lottery--how often do they sweep storage location to clear old ones?
$container['config']['session.cookie'] = 'laravel_session';
$container['config']['session.path'] = '/';
$container['config']['session.domain'] = null;
$container['config']['session.driver'] = 'file';
$container['config']['session.files'] =  'public/sessions';
// Now we need to fire up the session manager
$sessionManager = new SessionManager($container);
$container['session.store'] = $sessionManager->driver();
$container['session'] = $sessionManager;
// In order to maintain the session between requests, we need to populate the
// session ID from the supplied cookie
$cookieName = $container['session']->getName();
if (isset($_COOKIE[$cookieName])) {
    if ($sessionId = $_COOKIE[$cookieName]) {
        $container['session']->setId($sessionId);
    }
}
// Boot the session
$container['session']->start();
// END BOOTSTRAP---------------------------------------------------------------
