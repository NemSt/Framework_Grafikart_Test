<?php
namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminBlogAction
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
    /**
     * @var FlashService
     */
    private $flash;
    /**
     * @var SessionInterface
     */
    //private $session; /**/


    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable,
        SessionInterface $session,
        /**/
        FlashService $flash
    ) {//encore ici, c'est possible de le mettre directement parce que PhpDI va y injecter
                            //de façon automatique la session nécessaire à la construction de FlashService
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postTable->findPaginated(12, $params['p'] ?? 1);
        //$session = $this->session; /**/
        return $this->renderer->render('@blog/admin/index', compact('items', 'seesion'));
    }

    /**
     * Edit post
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        $item = $this->postTable->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            //$params['updated_at'] = date('Y-m-d'); /**/
            $validator = $this->getValidator($request); /*ajout*/
            if ($validator->isValid()) { /*ajout*/
                $this->postTable->update($item->id, $params);
                $this->flash->success('L\'article a bien été modifié');
                //$this->session->set('success', 'L\'article bla bla'); /**/
                return $this->redirect('blog.admin.index');
            } //***ajout***/
            $errors = $validator->getErrors(); /*ajout*/
            $params['id'] = $item->id; /*ajout*/
            $item = $params; /*ajout*/
        }
        /*return $this->renderer->render('@blog/admin/edit', compact('item'));*/
        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
    }

    /**
     * Create new post
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            /*$params = array_merge($params, [
                'updated_at' => date('Y-m-d'),
                'created_at' => date('Y-m-d')
            ]);*/

            $validator = $this->getValidator($request); /*ajout*/
            if ($validator->isValid()) { /*ajout*/
                $this->postTable->insert($params); /*ajout*/
                $this->flash->success('L\'article a bien été ajouté'); /*ajout*/
                return $this->redirect('blog.admin.index'); /*ajout*/
            }
            $item = $params; /*ajout*/
            $errors = $validator->getErrors(); /*ajout*/
        }
        $item = new Post(); /*ajout*/
        $item->created_at = new \DateTime(); /*ajout*/
        //$this->postTable->insert($params); /**/
        return $this->renderer->render('@blog/admin/create', compact('item', 'errors'));
        //return $this->renderer->render('@blog/admin/create', compact('item')); /**/
    }

    public function delete(Request $request)
    {
        $this->postTable->delete($request->getAttribute('id'));
        $this->flash->success('L\'article a bien été supprimé'); /*ajout*/
        return $this->redirect('blog.admin.index');
    }


    private function getParams(Request $request)
    {
        /*/**//*return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);****/
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at'/*ajout created*/]);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [ /*ajout*/
            'updated_at' => date('Y-m-d') /*ajout*/
        ]);
    }

    private function getValidator(Request $request) /*ajout function au complet*/
    {
        return (new Validator($request->getParsedBody())) //création de la règle de validation
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->dateTime('created_at')
            ->slug('slug');
    }
}
