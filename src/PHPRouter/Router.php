<?php

namespace PHPRouter;

/**
 * Contains methods and paths available to the application
 *
 * @example $router = new Router();
 *          $router->get("/a/:b", function (Request $request, Response $response) {});
 *          > "/a/:b" -> "b" is dynamic, route match with "/a/example", "/a/125", "/a/&x=5", etc.
 *
 * @example $router_1 = new Router();
 *          $router_2 = new Router();
 *          $route_2->get("/a/:b", function (Request $request, Response $response) {});
 *          $router_1->using($router_2);
 *          > $router_1->getMap(); -> ["GET" => ["path" => "/a/:b/" ...]]
 *
 * @example $router_1 = new Router();
 *          $router_2 = new Router();
 *          $router_3 = new Router();
 *          $route_2->get("/a/:b", function (Request $request, Response $response) {});
 *          $router_3->add("/a/:b","PROPFIND", function (Request $request, Response $response) {});
 *          $router_1->usingArray([$router_2, $router_3]);
 *          > $router_1->getMap(); -> ["GET" => ["path" => "/a/:b/" ...], "PROPFIND" => ["path" => "/a/:b/" ...]]
 *
 * @author Abraham Medina Carrillo <https://github.com/medina1402>
 */
class Router
{
    /**
     * Array of the properties of each route
     * @var array
     */
    private array $map;

    /**
     * Available methods, depends on the server, the standard methods available are:
     * "GET", "POST", "PUT", "PATCH" y "DELETE"
     * @var array|string[]
     */
    private array $methods = [
        "GET", "POST", "PUT", "PATCH", "DELETE", "COPY", "HEAD", "OPTIONS", "LINK", "UNLINK", "PURGE", "LOCK",
        "UNLOCK", "PROPFIND", "VIEW"
    ];

    /**
     * Array of the properties of each default route for method
     * @var array
     */
    private array $methodsDefault;

    /**
     * Initialize the contained methods and assign an array for the existence of all the methods.
     */
    public function __construct()
    {
        $this->map = array();
        $this->methodsDefault = array();
        foreach ($this->methods as $method) $this->map[$method] = array();
    }

    /**
     * Add external Router to internal map Router
     * @param Router $router
     * @return void
     */
    public function using(Router $router)
    {
        foreach ($router->map as $method => $item) {
            if(sizeof($item) > 0) foreach ($item as $route) {
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

    /**
     * Add multiple external Router to internal map Router
     * @param array $routers
     * @return void
     */
    public function usingArray(array $routers)
    {
        foreach ($routers as $router) {
            if (get_class($router) == "PHPRouter\Router") $this->using($router);
        }
    }

    /**
     * Mapping a Route and its properties on the internal Router map
     * @param string $path
     * @param string $method
     * @param object|null $callback
     * @param object|null $middleware
     * @return Router
     */
    private function insert(string $path, string $method, ?object $callback, ?object $middleware = NULL): Router
    {
        $this->map[$method][] = array(
            "route" => new Route($path),
            "callback" => $callback,
            "middleware" => $middleware,
            "response" => new Response(),
            "request" => new Request($method)
        );
        return $this;
    }

    /**
     * Add an existing path in the corresponding method, as long as it exists and the server supports it
     * @param string $path
     * @param string $method
     * @param object|null $callback
     * @param object|null $middleware
     * @return Router
     */
    public function add(string $path, string $method, ?object $callback, ?object $middleware = NULL): Router
    {
        $method = strtoupper($method);
        if (in_array($method, $this->methods)) return $this->insert($path, $method, $callback, $middleware);
        return $this;
    }

    /** Get the method map externally
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * Get the method map default externally
     * @return array
     */
    public function getMethodsDefault(): array
    {
        return $this->methodsDefault;
    }

    /**
     * Insert path for GET method
     * @param string $path
     * @param object|null $callback
     * @param object|null $middleware
     * @return $this
     */
    public function get(string $path, ?object $callback, ?object $middleware = NULL): Router
    {
        return $this->add($path, "GET", $callback, $middleware);
    }

    /**
     * Insert path for POST method
     * @param string $path
     * @param object|null $callback
     * @param object|null $middleware
     * @return $this
     */
    public function post(string $path, ?object $callback, ?object $middleware = NULL): Router
    {
        return $this->add($path, "POST", $callback, $middleware);
    }

    /**
     * Insert path for PUT method
     * @param string $path
     * @param object|null $callback
     * @param object|null $middleware
     * @return $this
     */
    public function put(string $path, ?object $callback, ?object $middleware = NULL): Router
    {
        return $this->add($path, "PUT", $callback, $middleware);
    }

    /**
     * Insert path for PATCH method
     * @param string $path
     * @param object|null $callback
     * @param object|null $middleware
     * @return $this
     */
    public function patch(string $path, ?object $callback, ?object $middleware = NULL): Router
    {
        return $this->add($path, "PATCH", $callback, $middleware);
    }

    /**
     * Insert path for DELETE method
     * @param string $path
     * @param object|null $callback
     * @param object|null $middleware
     * @return $this
     */
    public function delete(string $path, ?object $callback, ?object $middleware = NULL): Router
    {
        return $this->add($path, "DELETE", $callback, $middleware);
    }

    /**
     * Insert the path for the default method, if it is not found in some main method (GET, POST, etc.).
     * @param string $method
     * @param object|null $callback
     * @param object|null $middleware
     * @return $this|null
     */
    public function default(string $method, ?object $callback, ?object $middleware = NULL): Router
    {
        $method = strtoupper($method);
        if (!isset($this->methodsDefault[$method])) $this->methodsDefault[] = $method;

        $request = new Request($method);
        $this->methodsDefault[$method][] = array(
            "route" => new Route($request->getOriginalUrl()),
            "callback" => $callback,
            "middleware" => $middleware,
            "response" => new Response(),
            "request" => $request
        );
        return $this;
    }

    /**
     * Insert Route on all methods
     * @param string $path
     * @param object|null $callback
     * @param object|null $middleware
     * @return $this
     */
    public function all(string $path, ?object $callback, ?object $middleware = NULL): Router
    {
        foreach ($this->map as $method => $items) $this->add($path, $method, $callback, $middleware);
        return $this;
    }

}