<?php

use DI\ContainerBuilder;

//use Psr\Container\ContainerInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

$modules = [
    \App\Admin\AdminModule::class,
    \App\Blog\BlogModule::class
];

//Pour la gestion de l'injection des dépendances on va charger PHP-DI
$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$builder->addDefinitions(dirname(__DIR__) . '/config.php');
$container = $builder->build();

$app = new \Framework\App($container, $modules);

if (php_sapi_name() !== "cli") {
    //throw new Exception();
    //guzzle est un ensemble de méthodes conçues pour répondre au PSR7
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    //par contre, je dois convertir en output http l'objet response en psr7, et pour ça, j'utilise le package interop
    \Http\Response\send($response);
}
