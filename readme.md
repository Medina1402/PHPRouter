# PHPRouter
PHPRouter provides a fast and easy routing infrastructure for web development or API development, ideal for small projects.

## Install using Composer
The resource is added by executing the following script
```shell
composer require medina1402/php-router dev-main
```
When the dependency is added, a file must be created that loads the Composer content
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
...
```

## Basic routing
To create the routes, we need two main instances as shown below:
```php
$router = new Router();
$application = new Application($router);

$router->get("/", function () {
    echo "Hello world";
});

$application->run();
```
Although the above is possible, it is not recommended, for this two classes were created to perform a kind of Cast to the incoming values to the callback: **Request** and **Response**.
```php
$router = new Router();
$application = new Application($router);

$router->get("/", function (Request $req, Response $res) {
    $res->send("Hello world");
});

$application->run();
```
If we want to add routes we have two options, adding immediately after the last method:
```php
...
$router
    ->get("/", function (Request $req, Response $res) {
        $res->send("Get");
    })
    ->post("/", function (Request $req, Response $res) {
        $res->send("Post");
    });
...
```
or use the main variable:
```php
...
$router->get("/", function (Request $req, Response $res) {
    $res->send("Get");
});

$router->post("/", function (Request $req, Response $res) {
    $res->send("Post");
});
...
```

## Dynamic routes
There are times when the route is made up of dynamic data, for example when creating a user and wanting to access their information, one option is that their identifier is part of the URL:
```php
...
$router->get("/user/:id/profile", function (Request $req, Response $res) {
    $idUser = $req->getValue("id");
    $res->send("user id: " . $idUser);
});
...
```
By placing the colon we indicate that it must become a variable with the same name.
```php  
    match("/user/:id/profile", "/user/12548/profile") ==> true
    - id = 12548
    
    match("/:one/:two/:three/:four", "/a/b/c/d") ==> true
    - one   = "a"
    - two   = "b"
    - three = "c"
    - four  = "d"
     
    match("/:one/:two/:three/:four", "/a/b/c/d/e") ==> false
    match("/:one/:two/:three/:four", "/a/b/c/") ==> false
    match("/user/:id/profile", "/user/12548/other") ==> false
```

## Multiple router
We have the possibility to work each instance of Router separately and join them at the end, this opens the possibility of being able to work in architectures based on modules.
```php
// file: module/User
$routerUser = new Router();
$routerUser
    ->get("/user/:id", function (Request $req, Response $res) {
        $res->send("Get " . $req->getValue("id"));
    })
    ->post("/user/:id", function (Request $req, Response $res) {
        $res->send("Post " . $req->getValue("id"));
    })
    ->put("/user/:id", function (Request $req, Response $res) {
        $res->send("Put " . $req->getValue("id"));
    })
    ->delete("/user/:id", function (Request $req, Response $res) {
        $res->send("Delete " . $req->getValue("id"));
    });

// file: index
...
$router = new Router();
$application = new Application($router);
$router->using($routerUser);

$router->get("/", function (Request $req, Response $res) {
    $res->send("Hello world");
});

$application->run();
```
If we have several Routers we have the possibility of adding them through an Array to the new Router:
```php
...
$router->usingArray([$routerUser, $other1, $other2, ...]);
...
```

## Default routes
We can assign a default route to each method that we need, this route will have the respective callback and middleware, but it will only be executed if an existing route is not found
```php
...
$router = new Router();

$router->get("/user/:id", function (Request $req, Response $res) {
    $res->send("Hello world " . $req->getValue("id"));
}, $middleware);

$router->default("get", function (Request $req, Response $res) {
    $res->send("<div> <h1>Error 404<h1> <a href='/'>return home</a> </div>");
});
...
    - "/user/:id", "/user/125" => "Hello world 125"
    - "/user/:id", "/user"     => "<div> <h1>Error 404<h1> <a href='/'>return home</a> </div>"
```

## Middleware
The middleware is the one that runs before the main controller, it is suitable for checking parameters, since it receives the same parameters as the controller.
```php
...
$middleware = function (Request $req, Response $res) {
    if ( !$req->getValue("id") ) $res->send("User ID no found");
};

$router->get("/user/:id", function (Request $req, Response $res) {
    $res->send("Hello world " . $req->getValue("id"));
}, $middleware);

// OR

$router->get("/user/:id", function (Request $req, Response $res) {
    $res->send("Hello world " . $req->getValue("id"));
}, function (Request $req, Response $res) {
    if ( !$req->getValue("id") ) $res->send("User ID no found");
});
...
```
