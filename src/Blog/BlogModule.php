<?php
namespace App\Blog;

//use App\Blog\Actions\AdminBlogAction;
use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
//use App\Blog\Actions\BlogAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

//use Psr\Container\ContainerInterface;

//use Psr\Http\Message\ServerRequestInterface;


class BlogModule extends Module
{
    // pour pouvoir personnaliser les éléments pour le module
    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS =  __DIR__ . '/db/migrations';

    const SEEDS =  __DIR__ . '/db/seeds';

    public function __construct(ContainerInterface $container)
    {
        //le container se sert d'autowire pour aller chercher les éléments requis
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($container->get('blog.prefix'), PostIndexAction::class, 'blog.index');
        $router->get("$blogPrefix/{slug}-{id}", PostShowAction::class,'blog.show',
            [
                'slug' => '[a-z\-0-9]+',
                'id' => '[0-9]+'
            ]);
        $router->get("$blogPrefix/category/{slug}", CategoryShowAction::class, 'blog.category',
            [
                'slug' => '[a-z\-0-9]+'
            ]);
        //si le module admin a été chargé, alors on va appeler le crud
        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
        }
    }
}
