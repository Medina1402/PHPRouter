<?php

class Response {
    private $headers;


    public function __construct() {
        $this->headers = [];
    }


    public function sendStatus($code) {
        $this->status($code)->send();
        $this->end();
    }


    public function status($code) {
        http_response_code($code);
        return $this;
    }


    public function end() {
        exit(" ");
    }


    public function redirect($url, $permanent = NULL) {
        header("Location: $url", true, $permanent ? 301 : 302);
        $this->end();
    }


    public function download($path, $name = "default") {
        if( !file_exists("$path") ) {
            $this->status(200)->send("File $path no found");
            $this->end();
        }

        $this->headers["Content-Type"] = "application/octet-stream";
        $this->headers["Content-Transfer-Encoding"] = "Binary";
        $this->headers["Content-disposition"] = "attachment; filename=$name";
        $this->updateHeader();

        die(readfile("$path"));
    }


    public function render($path, $props = null) {
        if( file_exists($path)) include_once "$path";
        else $this->status(200)->send("File no found");
        $this->end();
    }


    public function send($data = null) {
        $this->updateHeader();
        if (isset($data)) echo $data;
        $this->end();
    }


    public function json($data = null) {
        $this->updateHeader();
        if (isset($data)) echo json_encode($data);
        $this->end();
    }


    private function updateHeader() {
        foreach (array_keys($this->headers) as $key) {
            header_remove($key);
            header($key . ":" . $this->headers[$key]);
        }
    }
}