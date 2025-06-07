# HttpFox

**HttpFox** is a PHP wrapper around cURL that simplifies HTTP requests like GET, POST, PUT, and DELETE. It provides easy cookie management, user-agent configuration, support for proxy and SSL certificates, and convenient file-size retrieval.

---

## Features

✅ Easy GET, POST, PUT, and DELETE requests  
✅ Built-in cookie management with optional file storage  
✅ Custom User-Agent support  
✅ Proxy support (with or without authentication)  
✅ SSL certificate support (PFX and PEM)  
✅ Get remote file sizes in different units (B, KB, MB, GB)  
✅ Set request timeouts and low-speed limits  
✅ Enable or disable verbose cURL output

---

## Installation

Use Composer:

```bash
composer require lucasarend/httpfox
```
Usage
```php
use LucasArend\HttpFox\HttpFox;

$httpFox = new HttpFox();

// GET request
$response = $httpFox->getURL('https://example.com');
echo $response;

// POST request
$postData = ['name' => 'John', 'email' => 'john@example.com'];
$response = $httpFox->sendPost('https://example.com/api', $postData);

// Enable or disable cookie files
$httpFox->useCookieFile(true);

// Set a custom cookie directory
$httpFox->setCookiePath('/path/to/cookies');

// Set proxy
$httpFox->setProxy('127.0.0.1', 8080, 'username', 'password');

// Disable SSL verification (not recommended in production)
$httpFox->disableSSL(true);

// Set a PFX certificate
try {
    $httpFox->setPFX('/path/to/certificate.pfx', 'password');
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Set custom headers
$httpFox->setHeaders([
    'Authorization: Bearer your_token',
    'Accept: application/json'
]);

```

## API Reference

| Method                                     | Description                                        |
| ------------------------------------------ | -------------------------------------------------- |
| `getURL($url)`                             | Makes a GET request.                               |
| `sendPost($url, $data)`                    | Makes a POST request with data.                    |
| `sendPUT($url, $data)`                     | Makes a PUT request with data.                     |
| `sendDELETE($url, $data)`                  | Makes a DELETE request with data.                  |
| `setCookiePath($path)`                     | Sets the directory for cookie files.               |
| `useCookieFile($bool)`                     | Enables or disables cookie file usage.             |
| `setProxy($host, $port, $user, $password)` | Sets up a proxy server.                            |
| `disableSSL($bool)`                        | Enables or disables SSL certificate verification.  |
| `setPFX($path, $password)`                 | Sets a PFX certificate.                            |
| `setPEM($path, $password)`                 | Sets a PEM certificate.                            |
| `setHeaders($headers)`                     | Sets custom HTTP headers.                          |
| `setUserAgent($ua)`                        | Sets a custom User-Agent.                          |
| `setTimeOut($seconds)`                     | Sets request timeout in seconds.                   |
| `setConnectTimeout($seconds)`              | Sets connection timeout in seconds.                |
| `get_file_size($url, $unit)`               | Gets the remote file size in bytes, KB, MB, or GB. |

## License
MIT