<?php

use App\Blog\BlogModule;
use function \Di\autowire;
use function \Di\get;

return [
    'blog.prefix' => '/blog' /*servirait à personnaliser le path pour le blog*/
    //initialisation qui n'est plus nécessaire parce que le container se sert d'autowire
    //BlogModule::class => autowire()->constructorParameter('prefix', get('blog.prefix'))
];
