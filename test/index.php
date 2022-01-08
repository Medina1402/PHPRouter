<?php

use PHPRouter\Request;
use PHPRouter\Response;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();
if (!isset($_SESSION['tasks'])) $_SESSION['tasks'] = [];

$router = new \PHPRouter\Router();
$router->get("/", function (Request $request, Response $response) {
    $response->render(__DIR__ . "/view/home.php", ["tasks" => $_SESSION['tasks']]);
});

$router->get("/:task", function (Request $request, Response $response) {
    $response->send($request->getValue("task"));
});

$router->post("/create", function (Request $request, Response $response) {
    $_SESSION['tasks'][] = $request->getParam("task");
    $response->json(["message" => "Create Task"]);
});

$router->delete("/delete", function (Request $request, Response $response) {
    $_SESSION['tasks'] = [];
    $response->json(["message" => "Delete all tasks"]);
});

$application = new \PHPRouter\Application($router);
$application->run();