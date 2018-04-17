<?php

namespace Framework\Router;

use Framework\Router;

class RouterTwigExtension extends \Twig_Extension
{

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path', [$this, 'pathFor'])
        ];
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateUri($path, $params);
    }
}
