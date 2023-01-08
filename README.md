<h1 align="center">🧾 Purl</h1>
<p align="center">PHP için basit URL oluşturma ve manipülasyon aracı.</p>

<p align="center">
<img src="https://img.shields.io/packagist/v/mlevent/purl?style=plastic"/>
<img src="https://img.shields.io/github/license/mlevent/purl?style=plastic"/>
<img src="https://img.shields.io/github/issues/mlevent/purl?style=plastic"/>
<img src="https://img.shields.io/github/last-commit/mlevent/purl?style=plastic"/>
<img src="https://img.shields.io/github/stars/mlevent/purl?style=plastic"/>
<img src="https://img.shields.io/github/forks/mlevent/purl?style=plastic"/>
</p>

## Kurulum

🛠️ Paketi composer ile projenize dahil edin;

```bash
composer require mlevent/purl
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
