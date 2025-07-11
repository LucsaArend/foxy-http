<?php

namespace LucasArend\HttpFox;

use Exception;

class HttpFox
{
    public $statusCode;
    public $verbose;
    private $responseText;
    private $ch;
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:136.0) Gecko/20100101 Firefox/136.0';
    private $headers;
    private $cookieDir;
    private $useCookieFile = true;
    private $cookieFile = 'cookie.txt';
    public $error = false;
    public $errorMessage = '';

    public function __construct($ch = null)
    {
        if ($ch) {
          $this->ch = $ch;
        } else {
          $this->ch = curl_init();
        }

        $multCrawler = getenv('HTTP_MULTI_CRAWLER') ?? false;
        if ($multCrawler && $this->useCookieFile) {
            $cookieName = rand(1,99999) . '-' . date('Ymd-His') . '-cookie.txt';
            $cookieDir = $this->cookieDir ?? sys_get_temp_dir();
            $this->cookieFile = $cookieDir . DIRECTORY_SEPARATOR . $cookieName;
        } elseif ($this->useCookieFile) {
            $cookieDir = $this->cookieDir ?? sys_get_temp_dir();
            $cookieName = 'cookie-' . date('Ymd-His') . '.txt';
            $this->cookieFile = $cookieDir . DIRECTORY_SEPARATOR . $cookieName;
        }

        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_ENCODING , "gzip");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        if ($this->useCookieFile && isset($this->cookieFile)) {
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        }
    }
    /* The cookie is expected to be a json in the following format */
    /* {"cookie1": "value1", "cookie2": "value2"} */
    public function setCookiesByJson($jsonCookies) {
        $cookieString = '';
        foreach ($jsonCookies as $name => $value) {
            $cookieString .= $name . '=' . $value . '; ';
        }
        curl_setopt($this->ch, CURLOPT_COOKIE, rtrim($cookieString, '; '));
    }
    /**
     * Sets the directory where cookie files will be stored
     * @param string|null $path where cookies will be stored. If null, uses sys_get_temp_dir().
     */
    public function setCookiePath($path = null)
    {
        if ($path === null) {
            $this->cookieDir = sys_get_temp_dir();
        } else {
            $this->cookieDir = rtrim($path, DIRECTORY_SEPARATOR);
        }
    }

    /**
     * Enable or disable the use of cookie files. If disabled, cookies are stored only in memory.
     * @param bool $useFile
     */
    public function useCookieFile($useFile = true)
    {
        $this->useCookieFile = $useFile;
    }

    public function setEncoding($encoding)
    {
        curl_setopt($this->ch, CURLOPT_ENCODING, $encoding);
    }

    public function setProxy($host = '127.0.0.1', $port = 8888, $user = null, $password = null)
    {
        curl_setopt($this->ch, CURLOPT_PROXY, $host . ':' . $port);
        if ($user && $password) {
          curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $user . ':' . $password);
        }
    }

    public function getURL($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);

        $this->responseText = curl_exec($this->ch);

        if ($this->responseText === false) {
          throw new \Exception('Curl error: ' . curl_error($this->ch));
        }

        $this->statusCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        $this->checkErros();

        return $this->responseText;
    }

    public function sendPost($prURL,$prData){
        curl_setopt($this->ch, CURLOPT_URL,$prURL);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        if (is_array($prData)) {
            $prData = http_build_query($prData);
        }
        curl_setopt($this->ch, CURLOPT_USERAGENT,$this->userAgent);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,$prData);

        $this->responseText = curl_exec($this->ch);
        $this->checkErros();
        $this->statusCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        curl_setopt($this->ch, CURLOPT_POST, 0);
        return $this->responseText;
    }

    public function sendPUT($prURL, $prData)
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        $result = $this->sendPost($prURL,$prData);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, null);
        $this->checkErros();
        return $result;
    }
	
	public function sendDELETE($prURL, $prData)
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $result = $this->sendPost($prURL,$prData);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, null);
        $this->checkErros();
        return $result;
    }

    public function sendPATCH($prURL, $prData)
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        $result = $this->sendPost($prURL, $prData);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, null);
        $this->checkErros();
        return $result;
    }

    /* Return the remote file size in bytes */
    /* $unit suport KB MB GB */
    public function get_file_size($url,$unit = null)
    {
        curl_setopt($this->ch, CURLOPT_URL,$url);
        curl_setopt($this->ch, CURLOPT_NOBODY, TRUE);
        curl_exec($this->ch);
        $file_size = curl_getinfo($this->ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_setopt($this->ch, CURLOPT_NOBODY, FALSE);
        $this->statusCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        switch ($unit) {
            case 'KB':
                return $file_size / 1024;
            case 'MB':
                return $file_size / 1024 / 1024;
            case 'GB':
                return $file_size / 1024 / 1024 / 1024;
            default:
                return $file_size;
        }
    }

    public function enableResponseHeader($prBoolean = true)
    {
      curl_setopt($this->ch, CURLOPT_HEADER, $prBoolean);
    }

    public function disableSSL($prBool = false)
    {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $prBool);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $prBool);
    }

    public function setHeader($prHeader, $prValue)
    {
        curl_setopt($this->ch, $prHeader, $prValue);
    }

    /**
     * Sets the headers for the cURL request.
     *
     * @param array $prHeader An array of strings, where each item is a complete header in the format "Name: Value".
     *                        Example: ["Content-Type: application/json", "Authorization: Basic <base64>"]
     * @return void
     */
    public function setHeaders($prHeader) {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $prHeader);
    }

    public function setUserAgent($prUserAgent){
        $this->userAgent = $prUserAgent;
    }
    /**
     * Enables verbose output for cURL operations. This will output detailed information about the
     * cURL transfer to STDERR.
     *
     * @return void
     */
    public function enableVerbose()
    {
        curl_setopt($this->ch, CURLOPT_VERBOSE, true);
        curl_setopt($this->ch, CURLOPT_STDERR, $this->verbose);
    }
    public function disableVerbose()
    {
        curl_setopt($this->ch, CURLOPT_VERBOSE, false);
        curl_setopt($this->ch, CURLOPT_STDERR, $this->verbose);
    }

    public function getVerbose()
    {
        return $this->verbose;
    }
    /**
     * Sets the request timeout in seconds.
     *
     * @param int $prTimeOutInSeconds Timeout duration in seconds. Default value is 60.
     *
     * @return void
     */
    public function setTimeOut($prTimeOutInSeconds = 60)
    {
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $prTimeOutInSeconds);
    }
    public function setConnectTimeout($seconds = 10)
    {
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $seconds);
    }

    public function setLowSpeedLimit($bytesPerSecond, $seconds)
    {
        curl_setopt($this->ch, CURLOPT_LOW_SPEED_LIMIT, $bytesPerSecond);
        curl_setopt($this->ch, CURLOPT_LOW_SPEED_TIME, $seconds);
    }
    /**
     * Function to set a PFX certificate for a cURL resource.
     *
     * @param string $pfxPath Path to the PFX certificate file.
     * @param string $pfxPassword Password to access the PFX certificate.
     */
    public function setPFX($pfxPath,$pfxPassword,$pfxSSLCerType = 'P12')
    {
        if (!file_exists($pfxPath)) {
            throw new Exception("PFX file not found: $pfxPath");
        }

        curl_setopt($this->ch, CURLOPT_SSLCERT, $pfxPath);
        curl_setopt($this->ch, CURLOPT_SSLCERTTYPE, $pfxSSLCerType);
        curl_setopt($this->ch, CURLOPT_SSLCERTPASSWD, $pfxPassword);

        $this->error = curl_errno($this->ch);
        $this->errorMessage = curl_error($this->ch);

        if ($this->error) {
            // Handle potential errors here, e.g., logging, throwing exceptions
            throw new Exception("Error setting PFX: $this->errorMessage");
        }
    }
    /**
     * Function to set a PEM certificate for a cURL resource.
     *
     * @param string $pfxPath Path to the PEM certificate file.
     * @param string $pfxPassword Password to access the PEM certificate.
     */
    public function setPEM($pemPath,$pemPassword = null)
    {
        curl_setopt($this->ch, CURLOPT_SSLCERT, $pemPath);
        curl_setopt($this->ch, CURLOPT_SSLCERTTYPE, 'PEM');
        if (!is_null($pemPassword)) {
            curl_setopt($this->ch, CURLOPT_SSLCERTPASSWD, $pemPassword);
        }
    }

    private function checkErros() {
        $this->error = curl_errno($this->ch);
        $this->errorMessage = curl_error($this->ch);

        if ($this->error) {
            throw new Exception("HttpFox Error: $this->errorMessage");
        }
    }

    public function close()
    {
        if ($this->ch) {
            curl_close($this->ch);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

}
