<?php

use App\Blog\BlogModule;
use function \Di\autowire;
use function \Di\get;

return [
    'blog.prefix' => "/blog",
    BlogModule::class => autowire()->constructorParameter('prefix', get('blog.prefix'))
];
