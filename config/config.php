<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router\RouterTwigExtension;

return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'monsupersite',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
      \DI\get(RouterTwigExtension::class)
    ],
    \Framework\Router::class => \DI\autowire(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class)
];

//Initialisation du renderer pour que les views puissent être adaptées
//$renderer = new \Framework\Renderer\TwigRenderer(dirname(__DIR__) . '/views');
//$renderer->addPath(dirname(__DIR__) . '/views');
//$loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/views');
//$twig = new Twig_Environment($loader, []);