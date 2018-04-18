<?php
namespace App\Blog;

use App\Blog\Actions\BlogAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

//use Psr\Container\ContainerInterface;

//use Psr\Http\Message\ServerRequestInterface;


class BlogModule extends Module
{
    // pour pouvoir personnaliser les éléments pour le module
    const DEFINITIONS = __DIR__ . '/config.php'; // entre autres le préfixe à utiliser
    const MIGRATIONS = __DIR__ . '/db/migrations';
    const SEEDS = __DIR__ . '/db/seeds';

    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        //le container se sert d'autowire pour aller chercher les éléments requis
        //$this->renderer = $this->container->get('renderer');
        $renderer->addPath('blog', __DIR__ . '/views');
        $router->get($prefix, BlogAction::class, 'blog.index');
        $router->get(
            $prefix . '/{slug}-{id}',
            BlogAction::class, //se servira du container
            'blog.show',
            [
                'slug' => '[a-z\-0-9]+',
                'id' => '[0-9]+'
            ]
        );
    }

    /*public function __construct(ContainerInterface $container)
    {
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($container->get('blog.prefix'), PostIndexAction::class, 'blog.index');
        $router->get("$blogPrefix/{slug:[a-z\-0-9]+}-{id:[0-9]+}", PostShowAction::class, 'blog.show');
        $router->get("$blogPrefix/category/{slug:[a-z\-0-9]+}", CategoryShowAction::class, 'blog.category');

        /*if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
        }*/
    //}

//les fonctions ci-dessous ont été déplacées pour des raisons de logique (actions)
    //public function index(ServerRequestInterface $request): string/*ResponseInterface*/
   // {
        //return $this->renderer->render('@blog/index');
    //}

   // public function show(ServerRequestInterface $request): string
    //{

       // return $this->renderer->render('@blog/show', [
            //'slug' =>$request->getAttribute('slug')
       // ]);
    //}
}
