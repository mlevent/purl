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

    echo $url->getParams('sort'); // string
    echo $url->getParams('sort', true); // array
    echo $url->getPath(); // ex./category/electronics/telephone
    echo $url->getPath(0); // category
    echo $url->searchValue('blue'); // boolean
    echo $url->isParam('color'); // booelan
    echo $url->getCurrent(); // current url