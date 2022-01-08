<?php

namespace PHPRouter;

/**
 * Contains the main router and executes the functions corresponding to the current path (url)
 *
 * @example $application = new Application();
 *          $router = $application->getRouter();
 *          $route->get("/a/:b", function (Request $request, Response $response) {});
 *          $application->run();
 *
 * @example $router = new Router();
 *          $route->get("/a/:b", function (Request $request, Response $response) {});
 *          $application = new Application($router);
 *          $application->run();
 *
 * @author Abraham Medina Carrillo <https://github.com/medina1402>
 */
class Application
{
    /**
     * Application main router
     * @var Router
     */
    private Router $router;

    /**
     * Generation or allocation of the main router
     * @param Router|null $router
     */
    public function __construct(?Router $router = null)
    {
        if ($router) $this->router = $router;
        else $this->router = new Router();
    }

    /**
     * Get route externally
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Method extraction and execution determined by method and path
     * @return void
     */
    public function run()
    {
        $method = explode("?", $_SERVER["REQUEST_METHOD"])[0];
        $path = explode("?", $_SERVER["REQUEST_URI"])[0];

        if (!$this->findRoute($method, $path)) {
            echo json_encode([
                "error" => "no found"
            ]);
        }
    }

    /**
     * Find and execute methods for current Route
     * @param string $method
     * @param string $path
     * @return bool
     */
    private function findRoute(string $method, string $path): bool
    {
        foreach ($this->router->getMap()[$method] as $route) if ($route["route"]->match($path)) {
            Application::exec($route);
            return true;
        }

        if (isset($this->router->getMethodsDefault()[$method])) {
            Application::exec($this->router->getMethodsDefault()[$method][0]);
            return true;
        }

        return false;
    }

    /**
     * Execute Request, Response and Middleware for current Route
     * @param array $route
     * @return void
     */
    private static function exec(array $route)
    {
        Application::varsForRequest($route["request"], $route["route"]);

        if ($route["middleware"] != null) {
            call_user_func($route["middleware"], $route["request"], $route["response"]);
        }
        call_user_func($route["callback"], $route["request"], $route["response"]);
    }

    /**
     * Add route values to Request
     * @param Request $req
     * @param Route $route
     * @return void
     */
    private static function varsForRequest(Request &$req, Route $route)
    {
        foreach ($route->getValues() as $key => $item) {
            $req->setValue($key, $item["value"]);
        }
    }

}