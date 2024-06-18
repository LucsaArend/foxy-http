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
### MultCrawler
If you intend to use it for multiple crawlers or simultaneous requests that require cookies, set this option in your .env file.
```php
HTTP_MULTI_CRAWLER=true
```

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

### Simple Post
```php
$postData = 'name=Lucas';
$http->sendPost('https://www.blogger.com/about/?hl=pt-br',$postData);
//Write Page Return
echo $http->response;
```

### Simple Put
```php
$http->sendPUT('https://www.blogger.com/about/?post=1','putData');
```

### Simple Delete
```php
$http->sendDELETE('https://www.blogger.com/about/?post=1');
//Withe Post Data
$http->sendDELETE('https://www.blogger.com/about/?post=1','postData');
```

### Custum Headers
```php
$http->setHeader('header','value');
//Use array header
$http->setHeaders(['Content-Type: application/json','Accept: application/json']);
```

### Util
#### Get Remote File Size
Suported Size MB KB GB default return Bytes
```php
$size = $http->get_file_size('https://cdn.britannica.com/79/232779-004-9EBC7CB8/German-Shepherd-dog-Alsatian.jpg?s=1500x700&q=85','MB');
```
#### Disable SSL Check
This option disables the website's SSL certificate checks.
```php
$http->disableSSL();
```
#### Set TimeOut Request
Overrides the default timeout value set in the php.ini file.
```php
$http->setTimeOut(30);
```
#### Use PFX File in Request
Suporte type P12, Always pass the full path to the file.
```php
$http->setPFX('path','pass');
```
#### Use PEM certificate in Request
Always pass the full path to the file.
```php
//With password
$http->setPEM('path','pass');
//Not use password
$http->setPEM('path');
```
#### Custom User Agent
When you don't use this function, it will always use a Firefox version configured within the library itself.
```php
$http->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0');
```

### Proxy And Debug
#### Set Proxy
Case you need to use a proxy service or even see your request use this function, User and password are not mandatory.
```
$http->setProxy('Host',Port,'User','Password');
```
### Debug
#### Debug Request
To debug the requests you will need a program to intersperse these requests as a proxy server, I like to use [Fiddler]('https://www.telerik.com/fiddler').
<br />Example of a simple debug routine
```php
use LucasArend\HttpFox\HttpFox;

$http = new HttpFox();

$http->setProxy();//setProxy use default fiddler config

$http->getURL('https://www.blogger.com/about/?hl=pt-br');

//Write Page Return
echo $http->response;
```
#### Response headers
```php
$httpFox = new HttpFox();
$httpFox->enableResponseHeader(); //Enable response headers

$httpFox->enableResponseHeader(false); // Disable response headers
```
cURL verbose
```php
//Enable Verbose
$http->enableVerbose();
//Your request
$http->get('www.mysite.com.br');
//Get Verbose
$verbose = $http->getVerbose();
```
If need disable verbose
```php
$http->disableVerbose();
```

## License

The MIT License (MIT). Please see [License File](https://github.com/LucsaArend/foxy-http/blob/main/LICENSE) for more information.