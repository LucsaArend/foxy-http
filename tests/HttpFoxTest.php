<?php

use PHPUnit\Framework\TestCase;
use LucasArend\HttpFox\HttpFox;

/**
 * @covers \LucasArend\HttpFox\HttpFox
 */
class HttpFoxTest extends TestCase
{
    private $httpFox;
    private $curlHandle;

    protected function setUp(): void
    {
        $this->curlHandle = curl_init();
        $this->httpFox = new HttpFox($this->curlHandle);
    }

    protected function tearDown(): void
    {
        curl_close($this->curlHandle);
    }

    public function testSetCookiesByJson()
    {
        $cookies = ['session' => 'abc123', 'user' => 'john'];
        $this->httpFox->setCookiesByJson($cookies);
        $this->assertTrue(true, 'setCookiesByJson executed without error');
    }

    public function testSetCookiePath()
    {
        $path = sys_get_temp_dir() . '/cookies';
        $this->httpFox->setCookiePath($path);
        $this->assertTrue(true, 'setCookiePath executed without error');
    }

    public function testUseCookieFile()
    {
        $this->httpFox->useCookieFile(false);
        $this->assertTrue(true, 'useCookieFile executed without error');
    }

    public function testSetEncoding()
    {
        $this->httpFox->setEncoding('gzip');
        $this->assertTrue(true, 'setEncoding executed without error');
    }

    public function testSetProxy()
    {
        $this->httpFox->setProxy('127.0.0.1', 8888, 'user', 'pass');
        $this->assertTrue(true, 'setProxy executed without error');
    }

    public function testSetUserAgent()
    {
        $agent = 'MyTestAgent/1.0';
        $this->httpFox->setUserAgent($agent);
        $this->assertTrue(true, 'setUserAgent executed without error');
    }

    public function testSetTimeOut()
    {
        $this->httpFox->setTimeOut(5);
        $this->assertTrue(true, 'setTimeOut executed without error');
    }

    public function testSetConnectTimeout()
    {
        $this->httpFox->setConnectTimeout(5);
        $this->assertTrue(true, 'setConnectTimeout executed without error');
    }

    public function testSetLowSpeedLimit()
    {
        $this->httpFox->setLowSpeedLimit(10, 5);
        $this->assertTrue(true, 'setLowSpeedLimit executed without error');
    }

    public function testEnableResponseHeader()
    {
        $this->httpFox->enableResponseHeader(true);
        $this->assertTrue(true, 'enableResponseHeader executed without error');
    }

    public function testDisableSSL()
    {
        $this->httpFox->disableSSL(true);
        $this->assertTrue(true, 'disableSSL executed without error');
    }

    public function testSetHeader()
    {
        $this->httpFox->setHeader(CURLOPT_TIMEOUT, 10);
        $this->assertTrue(true, 'setHeader executed without error');
    }

    public function testSetHeaders()
    {
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer token'
        ];
        $this->httpFox->setHeaders($headers);
        $this->assertTrue(true, 'setHeaders executed without error');
    }

    public function testSetPFXThrowsException()
    {
        $this->expectException(Exception::class);
        $this->httpFox->setPFX('/invalid/path/to/cert.p12', 'password');
    }

    public function testSetPEMDoesNotThrowException()
    {
        $this->httpFox->setPEM('/invalid/path/to/cert.pem', 'password');
        $this->assertTrue(true, 'setPEM executed without error');
    }

    public function testEnableVerbose()
    {
        $this->httpFox->enableVerbose();
        $this->assertTrue(true, 'enableVerbose executed without error');
    }

    public function testDisableVerbose()
    {
        $this->httpFox->disableVerbose();
        $this->assertTrue(true, 'disableVerbose executed without error');
    }

    public function testSimpleGet()
    {
        $this->httpFox->getURL('https://www.php.net/');
        $this->assertEquals(200,$this->httpFox->statusCode, 'status code executed without error');
    }
}
