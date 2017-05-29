# Engine

A tiny little PHP web framework wrote over a Bank Holiday weekend.

```php
use Monolog\Logger;
use Engine\{Controller, Router};

/* Controllers are just plain classes that will be instantiated with a Request, Response and logger. */
final class HomepageController extends Controller
{
    public function index(): void
    {
        /* The response object can be used to render templates and send them to the user. */
        $this->response->render('index.html');
    }
}

$logger = new Logger('foo');

/* The router is the heart of Engine, mapping incoming requests by method and path to controller actions. */
$router = new Router($logger);
$router->get('', 'HomepageController', 'index');

session_name('foo');
session_start();

/* Request objects wrap up PHP's various superglobals. */
$request = new Request($_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER);

/* Response objects provide conveniences for sending headers and response bodies to the user. */
$loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new \Twig_Environment($loader);
$response = new Response($twig, $logger);

/* Actually route the incoming request. */
$router->route($request, $response);
```

## Why "Engine?"

Because the various blogging systems I wrote over 15 years ago were invariably called "Engine" too.

## License

Copyright © 2017 Paul Mucur

Distributed under the MIT License.
