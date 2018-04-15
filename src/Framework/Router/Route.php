<?php

namespace Framework\Router;

/**
 * Class Route
 * @package Framework\Router
 * Used to represent a matched route
 */
class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var array
     */
    private $params;

    /**
     * Route constructor.
     * @param string $name
     * @param callable $callback
     * @param array $params
     */
    public function __construct(string $name, callable $callback, array $params)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Retrive the URL parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
