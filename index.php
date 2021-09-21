<?php

    require __DIR__.'/vendor/autoload.php';

    $url = new \Mlevent\Purl();

    echo $url->path('news')
         ->params(['q' => 'latest', 'tags' => ['sport', 'health'], 'sort' => 'desc'])
         ->build();

    echo $url->base('https://www.google.com')
            ->path('search')
            ->params(['q' => 'php'])
            ->build();

    echo $url->base(false)
            ->path('search')
            ->params(['q' => 'php'])
            ->build();

    echo $url->params(['colors' => ['red', 'black'], 'page' => 1])
            ->deny('sort', 'page')
            ->push();

    echo $url->allow('gender', 'page')->push();

    var_dump($url->getParams()); // array
    var_dump($url->getParams('sort')); // array
    var_dump($url->getParams('sort', true)); // string
    var_dump($url->getAllowParams()); // array
    var_dump($url->getDenyParams()); // array
    var_dump($url->getPath()); // ex./category/electronics/telephone
    var_dump($url->getPath(0)); // category
    var_dump($url->isValue('black')); // boolean
    var_dump($url->isParam('colors')); // booelan
    var_dump($url->getCurrent()); // current url