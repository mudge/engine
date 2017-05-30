# Engine [![Build Status](https://travis-ci.org/mudge/engine.svg?branch=master)](https://travis-ci.org/mudge/engine)

A tiny little PHP web framework written over a Bank Holiday weekend.

**Current version:** Unreleased  
**Supported PHP versions:** 7.1

## Usage

With the following directory layout (`vendor` directory not shown):

```
.
├── src
│   └── HomepageController.php
└── public
│   └── index.php
└── templates
    └── index.html
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
        $this->response->render('index.html', ['csrf_token' => $this->session->crsfToken()]);

        /* Use convenience methods for common responses. */
        $this->response->notFound();
        $this->response->forbidden();
        $this->response->redirect('http://www.example.com');
    }
}
```

The public `public/index.php` entrypoint:

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Engine\Application;

$stream = new StreamHandler('php://stderr', Logger::DEBUG);
$logger = new Logger('myapplication');
$logger->pushHandler($stream);

$loader = new \Twig_Loader_Filesystem(__DIR__ . '/../templates');
$twig = new \Twig_Environment($loader);

$application = new Application('myapplication', $twig, $logger);

/* The router is the heart of Engine, mapping incoming requests by method and path to controller actions. */
$application->router->get('', 'MyApplication\HomepageController', 'index');

/* Request objects wrap up PHP's various superglobals. */
$request = $application->request();

/* Response objects provide conveniences for sending headers and response bodies to the user. */
$response = $application->response();

/* Actually serve the incoming request. */
$application->run($request, $response);
```

## Why "Engine?"

Because the various blogging systems I wrote over 15 years ago were invariably called "Engine" too.

## License

Copyright © 2017 Paul Mucur

Distributed under the MIT License.
