<?php
namespace Framework\Actions;

use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
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
     * @var Table
     */
    private $table;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var string
     */
    protected $messages = [
        'create' => "L'élément a bien été créé",
        'edit'   => "L'élément a bien été modifié"
    ];

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flash /*ajout*/
    ) {         //encore ici, c'est possible de le mettre directement parce que PhpDI va y injecter
                //de façon automatique la session nécessaire à la construction de FlashService
        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
        $this->flash = $flash; /*ajout*/
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
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
     * Display list of items
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findPaginated(12, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * Edit item
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($item->id, $params);
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;
        }

        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Create new item
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($params);
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $item = $params;
            $errors = $validator->getErrors();
        }

        //$params = $this->formParams(compact('item', 'errors'));
        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * @param Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request)//: ResponseInterface
    {
        $this->table->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Select params from request
     *
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);

        //return array_filter($request->getParsedBody(), function ($key) {
            //return in_array($key, []);
        //}, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Instantiate the validator
     *
     * @param Request $request
     * @return Validator
     */
    protected function getValidator(Request $request)
    {
        return new Validator($request->getParsedBody());
    }

    /**
     * Instantiate entity for the create process
     *
     * @return array
     */
    protected function getNewEntity()
    {
        return [];
    }

    /**
     * Get params to send to view
     *
     * @param $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
