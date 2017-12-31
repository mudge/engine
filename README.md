# Engine [![Build Status](https://travis-ci.org/mudge/engine.svg?branch=master)](https://travis-ci.org/mudge/engine)

A tiny little PHP web framework written over a Bank Holiday weekend.

**Current version:** Unreleased  
**Supported PHP versions:** 7.1, 7.2

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

It is the user's responsibility to create a `Router` instance, populate it with routes (using helpers such as `get`, `post`, `root`) and then call `route` with a `Request` and `Response`. The `Application` object helps with this by providing helpers for creating `Request` and `Response` objects from PHP's various superglobals and by initializing an empty `Router` by default (see [Usage](#usage) for more information).

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
$application->router->root('MyApplication\HomepageController', 'index');
$application->router->get('/foo', 'MyApplication\HomepageController', 'foo');
$application->router->post('/bar', 'MyApplication\HomepageController', 'bar');

/* Request objects wrap up PHP's various superglobals. */
$request = $application->request();

/* Response objects provide conveniences for sending headers and response bodies to the user. */
$response = $application->response();

/* Actually serve the incoming request. */
$application->router->route($request, $response);
```

## Why "Engine?"

Because the various blogging systems I wrote over 15 years ago were invariably called "Engine" too.

## License

Copyright © 2017 Paul Mucur

Distributed under the MIT License.
