<?php

namespace Framework;

//use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

//code sniffer pour détecter d'autres anomalies : "composer require squizlabs/php_codesniffer"
//git
class App
{
    /**
     * List of modules
     * @var array
     */
    private $modules = [];

    /**
     * Router
     * @var ContainerInterface
     */
    private $container;

    /**
     * App constructor.
     * @param ContainerInterface $container
     * @param string[] $modules
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        //$this->router = new Router();
        //if (array_key_exists('renderer', $dependencies)) {
            //$dependencies['renderer']->addGlobal('router', $this->router);
        //}
        //il faut initialiser chacun des modules pour pouvoir les conserver, mais également connaître les
        //différentes routes qui vont appeler un même module (d'où le router)
        foreach ($modules as $module) {
            //$this->modules[] = new $module($this->router, $dependencies['renderer']);
            $this->modules[] = $container->get($module);//($this->router, $dependencies['renderer']);
        }
    }

    //la méthode implémente des interfaces de guzzle qui permettent d'utiliser les objets request et response

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // Pour éliminer les / en fin d'url et rediriger; utilisation des variables globales du serveur
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())//on crée l'objet et on le retourne en appellant tout de suite certaines méthodes
                ->withStatus(301)//c'est la façon que guzzle a de faire une sorte de "set"
                ->withHeader('Location', substr($uri, 0, -1));
        }
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of response interface');
        }
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
