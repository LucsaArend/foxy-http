<?php

namespace LucasArend\HttpFox;

class HttpFox
{
    public $statusCode = null;

    private $verbose = null;

    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0';

    private $headers;

    public function __construct()
    {
        $this->ch = curl_init();
        $this->cookie = 'cookie.txt';
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_ENCODING , "gzip");
    }

    public function setEncoding($prEncode)
    {
        curl_setopt($this->ch, CURLOPT_ENCODING , $prEncode);
    }

    public function setProxy($prHost = '127.0.0.1',$prPort = 8888,$prUser = null,$prPassworld = null)
    {
        curl_setopt($this->ch, CURLOPT_PROXY, $prHost . ':'. $prPort);
        if (!is_null($prUser) && !is_null($prPassworld)) {
            curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $prUser . ':' . $prPassworld);
        }
    }

    public function getURL($url)
    {
        curl_setopt($this->ch, CURLOPT_URL,$url);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR,$this->cookie);

        curl_setopt($this->ch, CURLOPT_USERAGENT,$this->userAgent);

        $this->responseText = curl_exec($this->ch);
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
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,$prData);

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
        if ($prBoolean) {
            curl_setopt($this->ch, CURLOPT_HEADER, 1);
        } else {
            curl_setopt($this->ch, CURLOPT_HEADER, 0);
        }
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