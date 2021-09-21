# url-builder

PHP için basit URL oluşturma ve manipülasyon aracı

[![Total Downloads](https://poser.pugx.org/mlevent/url-builder/d/total.svg)](https://packagist.org/packages/mlevent/url-builder)
[![Latest Stable Version](https://poser.pugx.org/mlevent/url-builder/v/stable.svg)](https://packagist.org/packages/mlevent/url-builder)
[![Latest Unstable Version](https://poser.pugx.org/mlevent/url-builder/v/unstable.svg)](https://packagist.org/packages/mlevent/url-builder)
[![License](https://poser.pugx.org/mlevent/url-builder/license.svg)](https://packagist.org/packages/mlevent/url-builder)

## Kurulum

```
$ composer require mlevent/url-builder
```

## Örnek Kullanım

```php
require __DIR__.'/vendor/autoload.php';

$url = new \Mlevent\Purl();
```

## URL Oluşturma

```php
echo $url->path('news')
         ->params(['q' => 'latest', 'tags' => ['sport', 'health'], 'sort' => 'desc'])
         ->build();
```

```
http(s)://site.com/news?q=latest&tags=sport,health&sort=desc
```

```php
echo $url->base('https://www.google.com')
         ->path('search')
         ->params(['q' => 'php'])
         ->build();
```

```
https://www.google.com/search?q=php
```

```php
echo $url->base(false)
         ->path('search')
         ->params(['q' => 'php'])
         ->build();
```

```
/search?q=php
```

## Manipülasyon

Example URL = https://site.com/products/?colors=blue&sort=price&page=2

```php
echo $url->params(['colors' => ['red', 'black'], 'page' => 1])
         ->deny('sort', 'page')
         ->push();
```

```
https://site.com/products/?colors=blue,red,black&page=1
```

Example URL = http://site.com/products/?gender=male&color=blue,red,black&page=1

```php
echo $url->allow('gender', 'page')->push();
```

```
http://site.com/products/?gender=male&page=1
```

```php
var_dump($url->getParams()); // array
var_dump($url->getParams('sort')); // array
var_dump($url->getParams('sort', true)); // string
var_dump($url->getAllowParams()); // array
var_dump($url->getDenyParams()); // array
var_dump($url->getPath()); // ex./category/electronics/telephone
var_dump($url->getPath(2)); // category
var_dump($url->isValue('black')); // bool
var_dump($url->isParam('colors')); // bool
var_dump($url->getCurrent()); // current url
```

## Contributors

-   [mlevent](https://github.com/mlevent) Mert Levent - creator, maintainer
