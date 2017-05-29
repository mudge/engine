# Engine

A tiny little PHP web framework written over a Bank Holiday weekend.

**Current version:** Unreleased  
**Supported PHP versions:** 7.1

## Usage

With the following directory layout (`vendor` directory not shown):

```
.
├── boot.php
├── src
│   └── HomepageController.php
└── public
    └── index.php
```

A controller, `src/HomepageController.php` in a [PSR-4 namespace](http://www.php-fig.org/psr/psr-4/) `MyApplication`:

```php
<?php
declare(strict_types=1);

namespace MyApplication;

use Engine\Controller;

/* Controllers are just plain classes that will be instantiated with a Request, Response and logger. */
final class HomepageController extends Controller
{
    public function index(): void
    {
        /* Prevent CSRF attacks using tokens in form submissions. */
        $this->verifyCsrfToken();

        /* Query arguments or post data can be accessed through Parameters */
        $this->params()->fetch('name');

        /* The response object can be used to render templates and send them to the user. */
        $this->response->render('index.html');

        /* Use convenience methods for common responses. */
        $this->response->notFound();
        $this->response->forbidden();
        $this->response->redirect('http://www.example.com');
    }
}
```

A top-level `boot.php` script:

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Engine\{Router, Request, Response};

$logger = new Logger('myapplication');

/* The router is the heart of Engine, mapping incoming requests by method and path to controller actions. */
$router = new Router($logger);
$router->get('', 'MyApplication\HomepageController', 'index');

session_name('myapplication');
session_start();

/* Request objects wrap up PHP's various superglobals. */
$request = new Request($_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER);

/* Response objects provide conveniences for sending headers and response bodies to the user. */
$loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new \Twig_Environment($loader);
$response = new Response($twig, $logger);
```

The public `public/index.php` entrypoint:

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/../boot.php';

/* Actually route the incoming request. */
$router->route($request, $response);
```

## Why "Engine?"

Because the various blogging systems I wrote over 15 years ago were invariably called "Engine" too.

## License

Copyright © 2017 Paul Mucur

Distributed under the MIT License.
