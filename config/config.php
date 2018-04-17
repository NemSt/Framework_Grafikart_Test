<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router\RouterTwigExtension;

return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'monsupersite',
    //'database.charset' => 'UTF8',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
      \DI\get(RouterTwigExtension::class)
    ],
    \Framework\Router::class => \DI\autowire(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    //création de l'objet PDO qui permettra de ramener les articles à la vue
    \PDO::class => function (\Psr\Container\ContainerInterface $c)
    {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

];

//Initialisation du renderer pour que les views puissent être adaptées
//$renderer = new \Framework\Renderer\TwigRenderer(dirname(__DIR__) . '/views');
//$renderer->addPath(dirname(__DIR__) . '/views');
//$loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/views');
//$twig = new Twig_Environment($loader, []);