<?php
namespace App\Blog;

use App\Blog\Actions\BlogAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

//use Psr\Http\Message\ServerRequestInterface;


class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';
    //private $prefix = 'blog';


    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        //$this->renderer = $this->container->get('renderer');
        $renderer->addPath('blog', __DIR__ . '/views');
        $router->get($prefix, BlogAction::class, 'blog.index');
        $router->get(
            $prefix . "/{slug}",
            BlogAction::class,
            'blog.show',
            [
                'slug' => '[a-z\-0-9]+'
                //'id' => '[0-9]+'
            ]
        );
    }

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
