<?php
$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();
return [
  'paths' => [
    'migrations' => 'migrations',
    'seeds' => 'seeds'
  ],
  'migration_base_class' => '\Flexio\Migration\Migration',
  'environments' => [
    'default_migration_table' => 'migrations',
    'default_database' =>getenv('DB'),
    'dev' => [
      'adapter' => 'mysql',
      'host' => getenv('HOSTDB'),
      'name' => getenv('DB'),
      'user' => getenv('USERDB'),
      'pass' => getenv('PASSDB')
    ],
    'produccion' => [
      'adapter' => 'mysql',
      'host' => getenv('HOSTDB'),
      'name' => getenv('DB'),
      'user' => getenv('USERDB'),
      'pass' => getenv('PASSDB')
    ]
  ]
];
