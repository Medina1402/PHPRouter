<?php

namespace PHPRouter;

/**
 * Data to send to the client
 *
 * Use in callback and middleware:
 * @example $callback = function(Request $request, Response $response) {};
 *          $middleware = function(Request $request, Response $response) {};
 *          $router = new Router();
 *          $router->get("/a/:b", $callback, $middleware);
 *
 * @author Abraham Medina Carrillo <https://github.com/medina1402>
 */
class Response
{
    /**
     * Header for response
     * @var array
     */
    private array $headers;

    /**
     * Initialize header response
     */
    public function __construct()
    {
        $this->headers = [];
    }

    /**
     * Remove all fields to header
     * @return void
     */
    public function clearHeader()
    {
        $this->headers = [];
    }

    /**
     * Change field into header
     * @param string $key
     * @param $value
     * @return void
     */
    private function setHeader(string $key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Insert array headers into real header response
     * @return void
     */
    private function updateHeader()
    {
        foreach (array_keys($this->headers) as $key) {
            header_remove($key);
            header($key . ":" . $this->headers[$key]);
        }
    }

    /**
     * Change content-type of header
     * @param string $type
     * @return void
     */
    private function setContentType(string $type)
    {
        $this->setHeader("Content-Type", $type);
    }

    /**
     * Send alone status code for response
     * @param int $code
     * @return void
     */
    public function sendStatus(int $code)
    {
        $this->status($code)->send();
        $this->end();
    }

    /**
     * Change status code for response
     * @param int $code
     * @return $this
     */
    public function status(int $code): Response
    {
        http_response_code($code);
        return $this;
    }

    /**
     * End cycle life for response
     * @return void
     */
    public function end()
    {
        exit(" ");
    }

    /**
     * Change current location for client
     * @param string $url
     * @param int|null $permanent
     * @return void
     */
    public function redirect(string $url, int $permanent = NULL)
    {
        header("Location: $url", true, $permanent ? 301 : 302);
        $this->end();
    }

    /**
     * Download any type file
     * @param string $path
     * @param string $name
     * @return void
     */
    public function download(string $path, string $name = "default")
    {
        if( !file_exists("$path") ) {
            $this->status(200)->send("File $path no found");
            $this->end();
        }

        $this->setContentType("application/octet-stream");
        $this->setHeader("Content-Transfer-Encoding", "Binary");
        $this->setHeader("Content-disposition", "attachment; filename=$name");
        $this->updateHeader();

        die(readfile("$path"));
    }

    /**
     * Send HTML to client (render view)
     * @param string $path
     * @param array|null $props
     * @return void
     */
    public function render(string $path, array $props = null)
    {
        $this->setContentType("text/html; charset=UTF-8");
        $this->updateHeader();
        if(file_exists($path)) include_once "$path";
        else $this->status(200)->send("File no found");
        $this->end();
    }

    /**
     * Send plain text to client
     * @param string|null $data
     * @return void
     */
    public function send(string $data = null)
    {
        $this->updateHeader();
        if (isset($data)) echo $data;
        $this->end();
    }

    /**
     * Convert array to string, edit content-type and use method send
     * @param array|null $data
     * @return void
     */
    public function json(?array $data = null)
    {
        $this->setContentType("text/json");
        $this->send(json_encode($data));
    }

}