<?php

//use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
//use Framework\Router\RouterFactory;
use Framework\Router\RouterTwigExtension;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Twig\{
    /*CsrfExtension, FormExtension, */FlashExtension, PagerFantaExtension, TextExtension, TimeExtension
};

return [
    //'env' => \DI\env('ENV', 'production'),
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'monsupersite',
    //'database.charset' => 'UTF8',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class),
        \DI\get(PagerFantaExtension::class),
        \DI\get(TextExtension::class),
        \DI\get(TimeExtension::class),
        \DI\get(FlashExtension::class),
        //\DI\get(FormExtension::class),
        //\DI\get(CsrfExtension::class)
    ],
    SessionInterface::class => \DI\autowire(PHPSession::class),
    //CsrfMiddleware::class => \DI\autowire()->constructor(\DI\get(SessionInterface::class)),
    //Router::class => \DI\factory(RouterFactory::class),
    Router::class => \DI\autowire(),
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
 // MAILER
   //'mail.to'    => 'admin@admin.fr',
    //Swift_Mailer::class => \DI\factory(\Framework\SwiftMailerFactory::class)
];



//Initialisation du renderer pour que les views puissent être adaptées
//$renderer = new \Framework\Renderer\TwigRenderer(dirname(__DIR__) . '/views');
//$renderer->addPath(dirname(__DIR__) . '/views');
//$loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/views');
//$twig = new Twig_Environment($loader, []);