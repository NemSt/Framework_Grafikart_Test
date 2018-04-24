<?php

//use DI\ContainerBuilder;

//use Psr\Container\ContainerInterface;
use App\Admin\AdminModule;
use App\Blog\BlogModule;
use Framework\Middleware\{
    DispatcherMiddleware,
    MethodMiddleware,
    RouterMiddleware,
    TrailingSlashMiddleware,
    NotFoundMiddleware
};
use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\Whoops;

require dirname(__DIR__) . '/vendor/autoload.php';

$modules = [
    AdminModule::class,
    BlogModule::class
];

//Pour la gestion de l'injection des dépendances on va charger PHP-DI
//Construction du container/builder
/*$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
//Ajout des définitions au builder pour qu'il puisse charger les différents éléments
$builder->addDefinitions(dirname(__DIR__) . '/config.php');
$container = $builder->build();*/
//Injection de ces données dans l'application afin qu'elle puisse démarrer et fonctionner
//$app = new \Framework\App($container, $modules);
$app = (new \Framework\App(dirname(__DIR__) . '/config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);

if (php_sapi_name() !== "cli") {
    //throw new Exception();
    //guzzle est un ensemble de méthodes conçues pour répondre au PSR7
    $response = $app->run(ServerRequest::fromGlobals());
    //par contre, je dois convertir en output http l'objet response en psr7, et pour ça, j'utilise le package interop
    \Http\Response\send($response);
}
