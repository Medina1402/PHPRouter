## Enrutamiento basico
Para crear las rutas necesitamos dos instancias principales como se muestra a continuacion:
```php
$router = new Router();
$application = new Application($router);

$router->get("/", function () {
    echo "Hello word";
});

$application->run();
```
Aunque lo anterior es posible, no se recomienda, para ello se crearon dos clases para realizar una especie de Cast a los valores entrantes al callback, dichas clases son **Request** y **Response**, la forma adecuada de realizar el ejemplo anterior es la siguiente:
```php
$router = new Router();
$application = new Application($router);

$router->get("/", function (Request $req, Response $res) {
    $res->send("Hello word");
});

$application->run();
```
Si queremos agregar rutas tenemos dos opciones, el agregarlo inmediatamente despues del ultimo metodo:
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
o usar la variable principal:
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

## Rutas dinamicas
Existen ocaciones en las que la ruta esta conformada por datos dinamicos, por ejemplo al crear un usuario y querer acceder a su informacion, una opcion es que su identidicador forme parte de la URL.
```php
...
$router->get("/user/:id/profile", function (Request $req, Response $res) {
    $idUser = $req->getValue("id");
    $res->send("user id: " . $idUser);
});
...
```
Al colocar los dos puntos indicamos que debe convertirse en una variable con el mismo nombre, la ruta se evalua pero al colocar ":" al inicio el valor que correspondiente a la posicion se almacena.
```php
    // El match utilizado no representa una funcion valida, es solo para indicar que las rutas son equivalentes    
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

## Multiples Router
Tenemos la posibilidad de trabajar cada instancia de Router por separado y unirlos al final, esto abre la posibilidad de poder trabajar en arquitecturas basada en modulos:
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
Si contamos con varios Router tenemos la posibilidad de agregarlos mediante un Array al nuevo Router:
```php
...
$router->usingArray([$routerUser, $other1, $other2, ...]);
...
```

## Middleware
El middleware es el que se ejecuta antes del controlador principal, es idoneo para comprobar parametros, ya que recibe los mismos parametros que el controlador.
### Creacion de middleware
Para la creacion de un Middleware debemos crear una clase que implemente la interfaz **Middleware** definida en el kernel:
```php
class AuthMiddleware implements Middleware
```
El interface nos pedirá que la clase contenga un método estático **invoke** que se llamara antes de ejecutar el callback correspondiente.
```php
...
class AuthMiddleware implements Middleware {
    public function invoke(Request &$request, Response &$response) {}
}
```
### Agregar un middleware a una ruta
Cada ruta de nuestro Router tiene la posibilidad de recibir tres parámetros:
- **Path**
- **Callback**
- **Middleware**

Por defecto las rutas omiten el middleware, para activarlo solo debemos agregarlo como un tercer parámetro, pero solo debemos agregar la clase no una instancia o funcion.
```php
...
$router->get("/", function (Request $req, Response $res) {
    $res->send("Hello word");
}, AuthMiddleware::class);
...
```
### Enviar datos de un Middleware a un Callback
Contamos con la posibilidad de modificar cada parametro de **Request** y **Response** por lo que podemos detener el servicio y evitar entrar al callback (al realizar un envio), pero tambien podemos agregar datos para que el callback los lea, podemos utilizar los siguiente campos para ello:
- body
- header
- values
- params

Cada uno de los anteriores cuenta con un metodo para agregar o modificar valores.
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