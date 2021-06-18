<?php

include_once "kernel/Route.php";
include_once "kernel/Router.php";

class Application {
    private $router;


    public function __construct(Router $router) {
        $this->router = $router;
    }


    public function run() {
        $method = explode("?", $_SERVER["REQUEST_METHOD"])[0];
        $path = explode("?", $_SERVER["REQUEST_URI"])[0];

        if(!$this->findRoute($method, $path)) {
            echo json_encode([
                "error" => "no found"
            ]);
        }
    }


    private function findRoute($method, $path) {

        foreach ($this->router->getMap()[$method] as $route) {
            if($route["route"]->match($path)) {
                Application::exec($route);
                return true;
            }
        }

        return false;
    }


    private static function exec($route) {
        Application::varsForRequest($route["request"], $route["route"]);

        if(isset($route["middleware"])) {
            $middleware = new $route["middleware"];
            if(is_a($middleware, "Middleware")) {
                $middleware->invoke($route["request"], $route["response"]);
            }
        };

        call_user_func($route["callback"], $route["request"], $route["response"]);
    }


    private static function varsForRequest(Request &$req, Route $route) {
        foreach ($route->values as $key => $item) {
            $req->addValue($key, $item["value"]);
        }
    }
}