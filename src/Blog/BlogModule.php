<?php
namespace App\Blog;

use Framework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule
{
    //private $prefix = 'blog';
    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get(
            '/blog' . "/{slug}",
            [$this, 'show'],
            'blog.show',
            [
                'slug' => '[a-z\-0-9]+'
                //'id' => '[0-9]+'
            ]
        );
    }

    public function index(ServerRequestInterface $request): string/*ResponseInterface*/
    {
        return '<h1>Bienvenue sur le blog</h1>';
    }

    public function show(ServerRequestInterface $request): string
    {

        return '<h1>Bienvenue sur l\'article ' . $request->getAttribute('slug') . '</h1>';
    }
}
