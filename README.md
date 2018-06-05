# MiniPHP API framework
這是一個練習開發的API Framework，之前要開發一個PHP的RestfulAPI都會選擇使用Laravel，但由於Laravel太過於龐大，並不是每個專案都適用，所以自己嘗試開發一個比較簡易使用的PHP API Framework.

---
### Install
With [**Composer**](https://getcomposer.org/ "download Composer")
```
composer require minhsieh/mini-php
```

### Basic Usage
```php
require "vendor/autoload.php";

use MiniPHP\App;

$app = new App;

# Normal Get
$app->get('/', function() use($app){
    echo "<h1>Hello World</h1> This is index";
});

# Get URI input
$app->get('/:name', function($name) use($app){
    echo "Hello $name";
})

# Json Response
$app->get('/json', function() use($app){
    $app->json(['foo' => 'bar']);
});

# Response to all other
$app->respond( function() use ( $app ){
  return $app->html('<p> We have a problem </p>', 404);
});

$app->listen();
```


### Apache2 .htaccess
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
RewriteRule ^.*$ index.php [NC,L]
```
