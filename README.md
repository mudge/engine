# Engine [![Build Status](https://travis-ci.org/mudge/engine.svg?branch=master)](https://travis-ci.org/mudge/engine)

A tiny little PHP web framework written over a Bank Holiday weekend.

**Current version:** Unreleased  
**Supported PHP versions:** 7.1, 7.2

## Installation

```console
$ composer require mudge/engine:dev-master
```

## Design

It is tempting to think of the HTTP request response cycle as a pure function `f` that accepts some request (perhaps as a raw HTTP string or as some wrapper object) and produces a single response (again, perhaps as a raw string or a wrapper object):

```
+-------------------+     +---+     +----------------------------------------+
| GET / HTTP/2      | --> | f | --> | HTTP/2 200                             |
| Host: example.com |     +---+     | Content-Type: text/html; charset=utf-8 |
+-------------------+               |                                        |
       Request                      | <!DOCTYPE html>...                     |
                                    +----------------------------------------+
                                                     Response
```

However, this mental model isn't quite right as it's not true that all requests produce a single response all in one go. For example, it is possible for a response to be sent in chunks: perhaps a request immediately receives some headers in response before the body is returned in parts. It's also possible for a response to never end by continuously streaming (e.g. think of the Twitter streaming APIs).

Therefore, Engine takes an alternative approach: modelling the typical request response cycle as a function that takes _both_ a request and a response object, the latter of which is a sort of open file handle allowing the user to send headers, etc. at any point during processing. This means that responses are created purely through side-effects which is a messier way of thinking about it but maps more closely to reality.

```
+-------------------+     +---+
| GET / HTTP/2      | --> |   |
| Host: example.com |     |   |
+-------------------+     |   |
       Request            | f |
                          |   |
+-------------------+     |   |
|                   | --> |   |
+-------------------+     +---+
       Response
```

Engine uses the [Model-view-controller](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller) pattern but controllers are objects that are initialized with a `Request`, `Response` and a logger.

The `Request` is a value object offering access to the request URI, method, any `GET` or `POST` parameters and any cookies or session data.

The `Response` object allows you to send data in response to the request via various methods:

* `redirect(string $location): void`: send a redirect header;
* `header(string $header): void`: send any arbitrary header;
* `render(string $template, array $variables = []): void`: render a [Twig](https://twig.symfony.com/) template with the given variables;
* `notFound(): void`: return a `404 Not Found` response with a template called `404.html`;
* `forbidden(): void`: return a `403 Forbidden` response with a template called `403.html`;
* `methodNotAllowed(): void`: return a `405 Method Not Allowed` response with a template called `405.html`.

Note that all methods return `void` as they work purely through side-effects: namely, sending data to the client.

Controller actions are therefore typical methods on the controller instance with access to the `request`, `response` and `logger` (see [Usage](#usage) for an example). As these are plain PHP objects, all the usual techniques such as composition and inheritance are available for sharing behaviour between controllers.

As for how controllers are instantiated and actions called: the `Router` is responsible for this and contains a map of HTTP methods and paths to controller classes and actions, e.g.

```
+-------------+     +----------------------+
| GET /       | --> | HomeController#index |
+-------------+     +----------------------+

+-------------+     +---------------------------+
| POST /login | --> | SessionsController#create |
+-------------+     +---------------------------+
```

It is the user's responsibility to create a `Router` instance, populate it with routes (using helpers such as `get`, `post`, `root`) and then call `route` with a `Request` and `Response`.

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

A controller, `src/HomepageController.php` in a [PSR-4 namespace](http://www.php-fig.org/psr/psr-4/) `App`:

```php
<?php
declare(strict_types=1);

namespace App;

use Engine\Controller;

/* Controllers are just plain classes that will be instantiated with a Request, Response and logger. */
final class HomepageController extends Controller
{
    public function index(): void
    {
        /* Prevent CSRF attacks using tokens in form submissions. */
        $this->verifyCsrfToken();

        /* Query arguments or post data can be accessed through Parameters. */
        $this->params->fetch('name');

        /* The response object can be used to render templates and send them to the user. */
        $this->response->render('index.html', ['csrf_token' => $this->session->crsfToken()]);

        /* Or, use the convenience methods on the controller itself. */
        $this->renderForm('index.html');

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

/**
 * Ensure all encoding is in UTF-8.
 */
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Engine\{Router, Request, Response};

/**
 * Set up logging.
 *
 * By default, this will log INFO-level messages to ../log/app.log
 */
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../log/app.log', Logger::INFO));

/**
 * Set up templating.
 *
 * By default, this will load templates from ../templates and cache them
 * to ../tmp (you may want to disable the cache during development)
 */
$loader = new \Twig_Loader_Filesystem(__DIR__ . '/../templates');
$twig = new \Twig_Environment($loader, ['cache' => __DIR__ . '/../tmp']);

/**
 * Set up the request and response.
 */
$request = Request::fromGlobals();
$response = new Response($twig, $logger);

/**
 * Set up the router.
 *
 * Add any routes of your own here, e.g.
 *
 *     $router->get('/login', 'App\SessionsController', 'new');
 *     $router->post('/login', 'App\SessionsController', 'create');
 */
$router = new Router($logger);
$router->root('App\HomepageController', 'index');

/**
 * Serve the request with the response.
 */
$router->route($request, $response);
```

## Why "Engine?"

Because the various blogging systems I wrote over 15 years ago were invariably called "Engine" too.

## License

Copyright © 2017 Paul Mucur

Distributed under the MIT License.
