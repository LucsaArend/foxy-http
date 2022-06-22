# foxy-http

## About Package
Hey,
This is my first package, I'm still learning. Feel free to suggest improvements

## Installation

Router is available via Composer:

```bash
"lucasarend/http-fox": "^1.0"
```
or run
```bash
composer require lucasarend/http-fox
```

## Documentation
### Create Class
```php
<?php

use LucasArend\HttpFox\HttpFox;

$http = new HttpFox();
```

### Simple Get Page
```php
$http->getURL('https://www.blogger.com/about/?hl=pt-br');
//Write Page Return
echo $http->response;
```

## License

The MIT License (MIT). Please see [License File](https://github.com/LucsaArend/foxy-http/blob/main/LICENSE) for more information.