<?php

namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Aura\Router\RouterContainer;
use Aura\Router\Map;

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
    /**
     * @var Map
     */
    private $map;
    /**
     * @var RouterContainer
     */
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
     * @param string|null $name
     * @param array|null $tokens
     */
    public function get(string $path, $callable, ?string $name = null, array $tokens = [])
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
     * @param string $path
     * @param string|callable $callable
     * @param string|null $name
     * @param array|null $tokens
     */
    public function post(string $path, $callable, ?string $name = null, array $tokens = [])
    {
        if (!$name) {
            $name = '';
        }
        $this->map->post($name, $path, $callable)->tokens($tokens);
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string|null $name
     * @param array|null $tokens
     */
    public function delete(string $path, $callable, ?string $name = null, array $tokens = [])
    {

        $this->map->delete($name, $path, $callable)->tokens($tokens);
    }

    /**
     * Getting CRUD routes
     *
     * @param string $prefixPath
     * @param $callable
     * @param string $prefixName
     */
    public function crud(string $prefixPath, $callable, string $prefixName)
    {
        $this->get("$prefixPath", $callable, "$prefixName.index");
        $this->get("$prefixPath/new", $callable, "$prefixName.create");
        $this->post("$prefixPath/new", $callable);
        $this->get("$prefixPath/{id}", $callable, "$prefixName.edit", ['id' => '[0-9]+']);
        $this->post("$prefixPath/{id}", $callable, null, ['id' => '[0-9]+']);
        $this->delete("$prefixPath/{id}", $callable, "$prefixName.delete", ['id' => '[0-9]+']);
    }

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
     * @return string|null
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
