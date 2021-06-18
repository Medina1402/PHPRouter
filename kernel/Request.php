<?php

class Request {
    private $values;
    private $params;
    private $method;
    private $header;
    private $body;


    public function __construct($method) {
        $this->values = array();
        $this->method = $method;
        $this->params = $_REQUEST;
        $this->header = apache_request_headers();
        parse_str(file_get_contents("php://input"),$this->body);
    }


    public function getMethod() {
        return $this->method;
    }


    public function getHostName() {
        return $_SERVER["HTTP_HOST"];
    }


    public function getOriginalUrl() {
        return $_SERVER["REQUEST_URI"];
    }


    public function getBaseUrl() {
        $uri = pathinfo($this->getOriginalUrl());
        return $uri["dirname"];
    }


    public function getPath() {
        $uri = pathinfo($this->getOriginalUrl());
        $uri_explode = explode("?", $uri["basename"]);
        if(sizeof($uri) > 1) $uri = $uri_explode[0];
        else $uri = $uri["basename"];
        return $uri;
    }


    public function setValue($key, $value) {
        $this->values[$key] = $value;
    }


    public function getValue($key) {
        if(key_exists($key, $this->values)) {
            return $this->values[$key];
        }
        return null;
    }


    public function setParam($key, $value) {
        $this->params[$key] = $value;
    }


    public function getParam($key) {
        if(key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }


    public function setHeader($key, $value) {
        $this->header[$key] = $value;
    }


    public function getHeader($key) {
        if(key_exists($key, $this->header)) {
            return $this->header[$key];
        }
        return null;
    }


    public function getCookies($key) {
        if(isset($_COOKIE[$key])) return $_COOKIE[$key];
        return null;
    }


    public function setCookie($key, $value, $path = "/", $time = NULL) {
        if ($time !== null) setcookie($key, $value, $time, $path);
        else setcookie($key, $value, time() + 604800, $path); // 7 days default
    }


    public function clearCookies() {
        foreach ($_COOKIE as $key => $item) {
            $this->setCookie($key, "", time() - 3600);
        }
    }


    public function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }


    public function getXHR() {
        return $this->getHeader("XMLHttpRequest") !== null;
    }


    public function is() {
        return $this->getHeader("Content-Type");
    }
}