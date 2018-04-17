<?php

namespace Framework\Renderer;

interface RendererInterface
{

    /**
     * Add a path to render views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Render a view
     * Path from namespace or addPath()
     * $this->render('@blog/view');
     * $this->render('view');
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

    /**
     * Add global variables to any view
     *
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void;
}
