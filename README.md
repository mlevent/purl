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
$build = $url->path('news')
             ->params(['cat' => '11', 'tags' => ['sport', 'health'], 'sort' => 'desc'])
             ->build();
```

```
http(s)://site.com/news?cat=11&tags=sport,health&sort=desc
```

```php
$build = $url->base('https://www.google.com')
             ->path('search')
             ->params(['q' => 'php'])
             ->build();
```

```
https://www.google.com/search?q=php
```

```php
$build = $url->base(false)
             ->path('search')
             ->params(['q' => 'php'])
             ->build();
```

```
/search?q=php
```

## Manipülasyon

```
Tarayıcıdaki Örnek URL: http(s)://site.com/products/?colors=blue&sort=price&page=2
```

```php
$push = $url->params(['colors' => ['red', 'black'], 'page' => 1])
            ->deny('sort', 'page')
            ->push();
```

```
Tarayıcıdaki Örnek URL: http(s)://site.com/products/?gender=male&color=blue,red,black&page=1
```

```php
$push = $url->allow('gender')->push();
```

```
Çıktı: http(s)://site.com/products/?gender=male
```

```php
$search = $url->searchValue('blue');
```

```php
$isParam = $url->isParam('color');
```

```php
$currentUrl = $url->current();
```