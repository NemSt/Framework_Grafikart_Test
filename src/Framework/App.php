<?php

namespace Framework;

use DI\ContainerBuilder;
//use Interop\Http\ServerMiddleware\DelegateInterface;
//use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

//code sniffer pour détecter d'autres anomalies : "composer require squizlabs/php_codesniffer"
//git
//class App
class App implements RequestHandlerInterface, MiddlewareInterface
{
    /**
     * List of modules
     * @var array
     */
    private $modules = [];
    /**
     * @var string
     */
    private $definition;

    /**
     * Container
     * @var ContainerInterface
     */
    private $container; //instance du container interface

    /**
     * @var string[]
     */
    private $middlewares;

    /**
     * @var int
     */
    private $index = 0;



    // définition qui doit être chargée en premier et récupéréé
    public function __construct(string $definition)
    {
        $this->definition = $definition;

    }
    //Module = pour ajouter des fonctionnalités; Pipe = pour ajouter un comportement à la requête
    /**
     * Add a module
     *
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Add a middleware
     *
     * @param string $middleware
     * @return App
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this; //pour enchaîner les méthodes
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        return $response;
    }

    /*public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware(); //appel du premier middleware
        if (is_null($middleware)) {
            throw new \Exception('Aucun middleware n\'a intercepté cette requête');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }*/

    //la méthode run sert à initialiser les modules nécessaires au fonctionnement de l'application

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
    }

    /**
     * Build the container while calling all definitiions it needs
     * @return ContainerInterface
     * @throws \Exception
     */
    private function getContainer(): ContainerInterface //sorte de singleton; on build le container avec les déf
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }


    /**
     * Fetch a middleware from array
     * @return object
     */
    private function getMiddleware()
    {
        //s'il y a une valeur à l'index évalué, on récupère le middleware, sinon on renvoie null
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }

    //méthode qui va s'autoappeler et retourner une responseInterface
    /**
     * Handle the request and return a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware(); //appel du premier middleware
        if (is_null($middleware)) {
            throw new \Exception('Aucun middleware n\'a intercepté cette requête');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }
}
