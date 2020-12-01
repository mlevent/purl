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

$purl = new \Mlevent\Purl();

$build = $url->path('store')
             ->params([
                'age'   => '32',
                'color' => ['blue', 'red'],
                'sort'  => 'desc'])
             ->build();
```

```
/store?age=32&color=blue,red&sort=desc
```