<h1 align="center">🧾 Purl</h1>
<p align="center">PHP için URL oluşturma ve manipülasyon aracı.</p>

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

## URL Oluşturma

```php
use Mlevent\Purl;

$url = new Purl;

$args = [
    'category' => 'bestSellers', 
    'colors' => [
        'red', 
        'blue',
        'black',
    ], 
    'sort' => 'desc',
    'page' => 2,
];

/**
 * @output
 * https://github.com/shop/?category=bestSellers&colors=red,blue,black&sort=desc&page=2
 */
echo $url->path('shop')
         ->args($args)
         ->build();
         
/**
 * @output
 * https://google.com/shop/?category=bestSellers&colors=red,blue,black&sort=desc&page=2
 */
echo $url->baseUrl('https://google.com')
         ->path('shop')
         ->args($args)
         ->build();

/**
 * @output
 * shop/?category=bestSellers&colors=red,blue,black&sort=desc&page=2
 */
echo $url->baseUrl(false)
         ->path('shop')
         ->args($args)
         ->build();
```

## Manipülasyon

```php
/**
 * @current
 * https://github.com/products/?colors=blue&sizes=S,M,L&sort=price&page=2
 * 
 * @output
 * https://github.com/products/?colors=blue,red,black&sort=price
 */
echo $url->args(['colors' => ['red', 'black']])
         ->deny('sizes', 'page')
         ->push();
         
/**
 * @current
 * https://github.com/products/?colors=blue&sizes=S,M,L&sort=price&page=2
 * 
 * @output
 * https://github.com/products/?colors=blue&sizes=S,M,L
 */
echo $url->allow('colors', 'sizes')
         ->push();
```

## Metodlar

```php
/**
 * @param  nullable|string $arg
 * @return array
 */
$url->getArgs($arg);

/**
 * @return array
 */
$url->getAllowedArgs();

/**
 * @return array
 */
$url->getDeniedArgs();

/**
 * @return  string
 * @example /category/electronics/telephone
 */
$url->getPath();

/**
 * @return  string
 * @example category
 */
$url->getPath(0);

/**
 * @param  string $arg
 * @return boolean
 */
$url->hasArg($args);

/**
 * @param  string $value
 * @return boolean
 */
$url->hasValue($value);

/**
 * @return string
 */
$url->getCurrentUrl();
```
