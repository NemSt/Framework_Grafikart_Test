<?php
namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use App\Blog\Entity\Post;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;
    /**
     * @var PostTable
     */
    private $postTable;


    use RouterAwareAction;


    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function __invoke(Request $request)
    {
        //$slug = $request->getAttribute('slug');
        if ($request->getAttribute('id')) {
            return $this->show($request);
        }
        return $this->index($request);
    }

    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    /**
     * Show a post
     * @param Request $request
     * @return ResponseInterface|string
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function show(Request $request)/*: string*/
    {
        $slug = $request->getAttribute('slug');
        /*$query = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$request->getAttribute('id')]);
        $post = $query->fetch();*/
        $post = $this->postTable->find($request->getAttribute('id'));
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}
