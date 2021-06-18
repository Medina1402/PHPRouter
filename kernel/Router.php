<?php

include_once "Request.php";
include_once "Response.php";

class Router {
    private $map;


    public function __construct() {
        $this->map = array(
            "GET" => array(),
            "POST" => array(),
            "PUT" => array(),
            "PATCH" => array(),
            "DELETE" => array(),
            "COPY" => array(),
            "HEAD" => array(),
            "OPTIONS" => array(),
            "LINK" => array(),
            "UNLINK" => array(),
            "PURGE" => array(),
            "LOCK" => array(),
            "UNLOCK" => array(),
            "PROPFIND" => array(),
            "VIEW" => array()
        );
    }


    public function getMap() {
        return $this->map;
    }


    public function usingArray($routers) {
        foreach ($routers as $router) {
            $this->using($router);
        }
    }


    public function using(Router $router) {
        foreach ($router->getMap() as $method => $item) {
            if(sizeof($item) > 0) {
                foreach ($item as $route) {
                    $this->map[$method][] = array(
                        "route" => $route["route"],
                        "callback" => $route["callback"],
                        "middleware" => $route["middleware"],
                        "response" => $route["response"],
                        "request" => $route["request"]
                    );
                }
            }
        }
    }


    private function add(Route $path, $method, $callback, $middleware = NULL) {
        $this->map[$method][] = array(
            "route" => $path,
            "callback" => $callback,
            "middleware" => $middleware,
            "response" => new Response(),
            "request" => new Request($method)
        );
        return $this;
    }


    public function get($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "GET", $callback, $middleware);
    }


    public function post($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "POST", $callback, $middleware);
    }


    public function put($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "PUT", $callback, $middleware);
    }


    public function patch($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "PATCH", $callback, $middleware);
    }


    public function delete($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "DELETE", $callback, $middleware);
    }


    public function copy($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "COPY", $callback, $middleware);
    }


    public function head($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "HEAD", $callback, $middleware);
    }


    public function options($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "OPTIONS", $callback, $middleware);
    }


    public function link($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "LINK", $callback, $middleware);
    }


    public function unlink($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "UNLINK", $callback, $middleware);
    }


    public function purge($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "PURGE", $callback, $middleware);
    }


    public function lock($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "LOCK", $callback, $middleware);
    }


    public function unlock($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "UNLOCK", $callback, $middleware);
    }


    public function propfind($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "PROPFIND", $callback, $middleware);
    }


    public function view($path, $callback, $middleware = NULL) {
        return $this->add(new Route($path), "VIEW", $callback, $middleware);
    }


    public function all($path, $callback, $middleware = NULL) {
        foreach ($this->map as $method => $items) {
            $this->add(new Route($path), $method, $callback, $middleware);
        }
        return $this;
    }
}