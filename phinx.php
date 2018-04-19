<?php
require 'public/index.php';

$migrations = [];
$seeds = [];
// pour que chaque module puisse définir ses propres migrations
//foreach ($app->getModules() as $module) {
foreach ($modules as $module) {
    if ($module::MIGRATIONS) {
        $migrations[] = $module::MIGRATIONS;
    }
    if ($module::SEEDS) {
        $seeds[] = $module::SEEDS;
    }
}

return [
    'paths'        => [
        'migrations' => $migrations,
        'seeds'      => $seeds
    ],
    'environments' => [
        'default_database' => 'development',
        'development'      => [
            'adapter' => 'mysql',
            'host' => $app->getContainer()->get('database.host'),
            'name' => $app->getContainer()->get('database.name'),
            'user' => $app->getContainer()->get('database.username'),
            'pass' => $app->getContainer()->get('database.password')//,
            //'chars' => $app->getContainer()->get('database.charset')
        ]
    ]
];