<?php
//cette classe vise à ajouter à twig les vues qui seront nécessaires à l'administration
namespace App\Admin;

use Framework\Module;
use Framework\Renderer\RendererInterface;

class AdminModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer)
    {
        $renderer->addPath('admin', __DIR__ . '/views');
    }
}
