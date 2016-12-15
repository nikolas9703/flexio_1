<?php
require_once "vendor/autoload.php";
use Symfony\Component\Console\Application;
use Illuminate\Database\Console\Migrations;
use Pimple\Container;

$container = new Container();

$container['migration-table'] = 'migration';

$container['db-config'] = [
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'erps',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'   => 'public'
];

$container['filesytem'] = function ($c) {
    return new \Illuminate\Filesystem\Filesystem;
};

$container['composer'] = function ($c) {
    $composer = Mockery::mock('Illuminate\Foundation\Composer');
    $composer->shouldReceive('dumpAutoloads');
    return $composer;
};

$container['connection'] = function ($c) {
    $manager = new \Illuminate\Database\Capsule\Manager();
    $manager->addConnection($c['db-config']);
    $manager->setAsGlobal();
    $manager->bootEloquent();
    return $manager->getConnection('default')->getPdo();
};

$container['resolver'] = function ($c) {
    $r = new \Illuminate\Database\ConnectionResolver(['default' => $c['connection']]);
    $r->setDefaultConnection('default');
    return $r;
};

$container['migration-repo'] = function ($c) {
    return new \Illuminate\Database\Migrations\DatabaseMigrationRepository($c['resolver'], $c['migration-table']);
};

$container['migration-creator'] = function ($c) {
    return new \Illuminate\Database\Migrations\MigrationCreator($c['filesytem']);
};

$container['migrator'] = function ($c) {
    return new Illuminate\Database\Migrations\Migrator($c['migration-repo'], $c['resolver'], $c['filesytem']);
};

$container['install-command'] = function ($c) {
    $command = new Migrations\InstallCommand($c['migration-repo']);
    return $command;
};

$container['migrate-make-command'] = function ($c) {
    $command = new Migrations\MigrateMakeCommand($c['migration-creator'], $c['composer']);
    return $command;
};

$app = new Application("Jarvis, little brother of Artisan", "1.0");
$app->add($container['install-command']);
$app->add($container['migrate-make-command']);
//$app->run();
