<?php

namespace LucasArend\HttpFox;

class HttpFox
{
    private $ch;
    private $statusCode;
    private $responseText;
    private $verbose;
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0';
    private $headers;
    private $cookieFile = 'cookie.txt';

    public function __construct($ch = null)
    {
        if ($ch) {
          $this->ch = $ch;
        } else {
          $this->ch = curl_init();
        }

        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_ENCODING , "gzip");
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
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

        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR,$this->cookie);

        $this->responseText = curl_exec($this->ch);
        $this->statusCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        curl_setopt($this->ch, CURLOPT_POST, 0);
        return $this->responseText;
    }

    public function sendPUT($prURL, $prData)
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        $result = $this->sendPost($prURL,$prData);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, null);
        return $result;
    }
	
	public function sendDELETE($prURL, $prData)
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $result = $this->sendPost($prURL,$prData);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, null);
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

    /* @param  $prHeader array */
    public function setHeaders($prHeader) {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $prHeader);
    }

    public function setUserAgent($prUserAgent){
        $this->userAgent = $prUserAgent;
    }

    public function enableVerbose()
    {
        curl_setopt($this->ch, CURLOPT_VERBOSE, true);
        curl_setopt($this->ch, CURLOPT_STDERR, $this->verbose);
    }

    public function getVerbose()
    {
        return $this->verbose;
    }

}
