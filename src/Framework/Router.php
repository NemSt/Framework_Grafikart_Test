<?php

namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Aura\Router\RouterContainer;

//use Zend\Expressive\Router\FastRouteRouter;
//use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Class Router
 * @package Framework
 * Register and match routes
 */
class Router
{
    //private $router;
    private $map;
    private $routerContainer;
    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->routerContainer = new RouterContainer();
        $this->map = $this->routerContainer->getMap();
        //$this->router = new FastRouteRouter();
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     * @param array|null $tokens
     */
    public function get(string $path, $callable, string $name, ?array $tokens = [])
    {
        $this->map->get($name, $path, $callable)->tokens($tokens);
    }

    /*/**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
   /* public function match(ServerRequestInterface $request): ?Route {
        $result = $this->router->match($request);
        return new Route(
            $result->getMatchedRouteName(),
            $result->getMatchedMiddleware(),
            $result->getMatchedParams()
        );
    }*/
    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $matcher = $this->routerContainer->getMatcher();
        $route = $matcher->match($request);
        if ($route) {
            return new Route(
                $route->name,
                $route->handler,
                $route->attributes
            );
        }
        return null;
    }

    /**
     * @param string $name
     * @param array $params
     * @param array $queryParams
     * @return null|string
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $generator = $this->routerContainer->getGenerator();
        $uri = $generator->generate($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }
}
