<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
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
     * @var Router
     */
    private $router;

    /**
     * App constructor.
     * @param string[] $modules Liste des modules à charger
     */
    public function __construct(array $modules = [])
    {
        $this->router = new Router();
        //il faut initialiser chacun des modules pour pouvoir les conserver, mais également connaître les
        //différentes routes qui vont appeler un même module (d'où le router)
        foreach ($modules as $module) {
            $this->modules[] = new $module($this->router);
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
        $route = $this->router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $response = call_user_func_array($route->getCallback(), [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of response interface');
        }
    }

           /* Pour appeler une route dans un module
           $router->get(
            $prefix . "article/{slug}-{id}",
            PostShowAction::class,
            'home.show',
            [
            'slug' => '[a-z\-0-9]+',
            'id' => '[0-9]+'
            ]
            );*/
}
