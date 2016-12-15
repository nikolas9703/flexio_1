<?php

use function DI\object;


return [
    // Bind an interface to an implementation
    FlexioSession::class => object(\Illuminate\Session\SessionServiceProvider::class)
    // Configure Twig
    /*

     /*Twig_Environment::class => function () {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../src/SuperBlog/Views');
        return new Twig_Environment($loader);
    },*/
];
