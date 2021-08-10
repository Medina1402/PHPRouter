# PHPRouter
PHPRouter provides a fast and easy routing infrastructure for web development or API development, ideal for small projects.

Example: https://github.com/Medina1402/PHPRouter-example

## Basic routing
To create the routes, we need two main instances as shown below:
```php
$router = new Router();
$application = new Application($router);

$router->get("/", function () {
    echo "Hello word";
});

$application->run();
```
Although the above is possible, it is not recommended, for this two classes were created to perform a kind of Cast to the incoming values to the callback: **Request** and **Response**.
```php
$router = new Router();
$application = new Application($router);

$router->get("/", function (Request $req, Response $res) {
    $res->send("Hello word");
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
    $res->send("Hello word");
});

$application->run();
```
If we have several Routers we have the possibility of adding them through an Array to the new Router:
```php
...
$router->usingArray([$routerUser, $other1, $other2, ...]);
...
```

## Middleware
The middleware is the one that runs before the main controller, it is suitable for checking parameters, since it receives the same parameters as the controller.
### Middleware creation
To create a Middleware we must create a class that implements the **Middleware** interface defined in the kernel:
```php
class AuthMiddleware implements Middleware
```
The interface will ask us that the class contains a static method **invoke** that will be called before executing the corresponding callback..
```php
...
class AuthMiddleware implements Middleware {
    public function invoke(Request &$request, Response &$response) {}
}
```
### Add a middleware to a route
Each route of our Router has the possibility of receiving three parameters:
- **Path**
- **Callback**
- **Middleware**

By default the routes omit the middleware, to activate it we only have to add it as a third parameter, but we only have to add the class.
```php
...
$router->get("/", function (Request $req, Response $res) {
    $res->send("Hello word");
}, AuthMiddleware::class);
...
```
### Send data from a Middleware to a Callback
We have the possibility to modify each parameter of **Request** and **Response** so that we can stop the service and avoid entering the callback (when sending), but we can also add data so that the callback reads it , we can use the following fields for it:
- body
- header
- values
- params

Each of the above has a method to add or modify values.
```php
...
class AuthMiddleware implements Middleware {
    public function invoke(Request &$request, Response &$response) {
        $request->addValue("message", "Hello word");
    }
}

...

$router->get("/", function (Request $req, Response $res) {
    $res->send($req->getValue("message")); // "Hello word"
}, AuthMiddleware::class);
...
```
