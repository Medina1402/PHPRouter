<?php

include_once "kernel/Request.php";
include_once "kernel/Response.php";

interface Middleware {
    public function invoke(Request &$request, Response &$response);
}