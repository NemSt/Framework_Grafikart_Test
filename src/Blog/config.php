<?php

use App\Blog\BlogModule;
use function \Di\autowire;
use function \Di\get;

//initialisation qui n'est plus nécessaire parce que le container se sert d'autowire
//BlogModule::class => autowire()->constructorParameter('prefix', get('blog.prefix'))

return [
    'blog.prefix' => '/blog',
    'admin.widgets' => \DI\add([
        get(\App\Blog\BlogWidget::class)
    ])
];
