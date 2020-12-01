<?php

    require __DIR__.'/vendor/autoload.php';

    use mlevent\purl;

    $purl = new mlevent\purl();

    $push = $purl->params([

        'filter' => ['15'],
        'sort'   => 'desc',
        'page'   => 1

    ])->deny('page', 'filter')->push();

    $build = $purl->path('contact')->params([

        'sort'   => 'desc',
        'filter' => ['299', '123']

    ])->build();

    print_r($purl->searchValue('store'));
    print_r($push);
    print_r($build);
    print_r($purl->current());