<?php

namespace PHPRouter;

/**
 * Information contained for the client's request and server-related values
 *
 * Use in callback and middleware:
 * @example $callback = function(Request $request, Response $response) {};
 *          $middleware = function(Request $request, Response $response) {};
 *          $router = new Router();
 *          $router->get("/a/:b", $callback, $middleware);
 *
 * @author Abraham Medina Carrillo <https://github.com/medina1402>
 */
class Request
{
    /**
     * Contains values dynamic path
     * @example "/a/:b", current_path = "/a/data" => getValue("b") = "data"
     * @var array
     */
    private array $values;

    /**
     * Contains query values
     * @example "/a/:b?data=c" => getParam("data") = "c"
     * @var array
     */
    private array $params;

    /**
     * Current valid method name
     * @var string
     */
    private string $method;

    /**
     * Client request header
     * @var array|null
     */
    private array $header;

    /**
     * Client request body
     * @var array
     */
    private array $body;

    /**
     * Initialize all values for current client request
     * @param string $method
     */
    public function __construct(string $method) {
        $this->values = array();
        $this->body = array();
        $this->method = $method;
        $this->params = $_REQUEST;
        $this->header = apache_request_headers();
        parse_str(file_get_contents("php://input"),$this->body);
    }

    /**
     * Get name current method
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get name current host of server
     * @return string
     */
    public function getHostName(): string
    {
        return $_SERVER["HTTP_HOST"];
    }

    /**
     * Get path including parameters
     * @example "/a/b?data=c" => "/a/b?data=c"
     * @return string
     */
    public function getOriginalUrl(): string
    {
        return $_SERVER["REQUEST_URI"];
    }

    /**
     * Get dirname path
     * @example "/a/:b" => "/a"
     * @return string
     */
    public function getBaseUrl(): string
    {
        $uri = pathinfo($this->getOriginalUrl());
        return $uri["dirname"];
    }

    /**
     * Get path ignoring parameters
     * @example "/a/b?data=c" => "/a/b"
     * @return string
     */
    public function getPath(): string
    {
        $uri = pathinfo($this->getOriginalUrl());
        $uri_explode = explode("?", $uri["basename"]);
        if(sizeof($uri) > 1) $uri = $uri_explode[0];
        else $uri = $uri["basename"];
        return $uri;
    }

    /**
     * Change value of a "value"
     * @param string $key
     * @param $value
     * @return void
     */
    public function setValue(string $key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * Get value of a "value" from client request
     * @param string $key
     * @return mixed|null
     */
    public function getValue(string $key)
    {
        if(key_exists($key, $this->values)) return $this->values[$key];
        return null;
    }

    /**
     * Get all values from client request
     * @return array
     */
    public function getValues(): ?array
    {
        return $this->values;
    }

    /**
     * Change value of a param
     * @param string $key
     * @param $value
     * @return void
     */
    public function setParam(string $key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * Get value of a param from client request
     * @param string $key
     * @return mixed|null
     */
    public function getParam(string $key)
    {
        if(key_exists($key, $this->params)) return $this->params[$key];
        return null;
    }

    /**
     * Get all params from client request
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get all body from client request
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Change value of a header
     * @param string $key
     * @param $value
     * @return void
     */
    public function setHeader(string $key, $value)
    {
        $this->header[$key] = $value;
    }

    /**
     * Get value of a header from client request
     * @param string $key
     * @return mixed|null
     */
    public function getHeader(string $key)
    {
        if(key_exists($key, $this->header)) return $this->header[$key];
        return null;
    }

    /**
     * Get all headers from client request
     * @return array|null
     */
    public function getHeaders(): ?array
    {
        return $this->header;
    }

    /**
     * Change value of a cookie
     * @param string $key
     * @param $value
     * @param string $path
     * @param int|null $time
     * @return void
     */
    public function setCookie(string $key, $value, string $path = "/", int $time = NULL)
    {
        if ($time !== null) setcookie($key, $value, $time, $path);
        else setcookie($key, $value, time() + 604800, $path); // 7 days default
    }

    /**
     * Get value of a cookie from client request
     * @param string $key
     * @return string
     */
    public function getCookie(string $key): ?string
    {
        if(isset($_COOKIE[$key])) return $_COOKIE[$key];
        return null;
    }

    /**
     * Get all cookies from client request
     * @return array
     */
    public function getCookies(): array
    {
        return $_COOKIE;
    }

    /**
     * Delete all cookies from the application in the client's request
     * @return void
     */
    public function clearCookies()
    {
        foreach ($_COOKIE as $key => $item) {
            $this->setCookie($key, "", time() - 3600);
        }
    }

    /**
     * Get IP value from client request
     * @return string
     */
    public function getIP(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDER'];
    }

    /**
     * Confirm request is type XMLHttpRequest
     * @return bool
     */
    public function isXHR(): bool
    {
        return $this->getHeader("XMLHttpRequest") !== null;
    }

    /**
     * Get current value content-type
     * > text/html, text/xml, application/octet-stream, etc.
     * @return string|null
     */
    public function is(): ?string
    {
        return $this->getHeader("Content-Type");
    }

}