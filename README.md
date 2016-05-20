Exédra
======
A multi-tier nestful routing oriented PHP Microframework.

p/s : About to be released on stable version

Introduction
======
This PHP microframework focuses on nestable/groupable routing, mainly through Http Request components, with middlewarable routes and which hopefully will provide much modular application execution. Route is unique, and identifiable by name, tag, and queriable by Http Request. Imagine developing an application down the depth without losing the identity of the executions you planned, controlling the routes hierarchically with your own layers of middlewares.

The goal is to be contextual, explicitful while being simple and minimal at the same time. Hence the focus on just being microframework. It will not surprise you with unintended heart attacks, but of course, number of what it intently can do would definitely surprise you. 

Imagine building a plane while flying it!

Installation
======
### Composer
===
Install rosengate/exedra through your console, in your project folder.
~~~
composer require rosengate/exedra dev-master
~~~

Documentation
===
Documentation and the homebase for exedra is currently hosted here : http://exedra.rosengate.com (currently not updated)

Booting up \Exedra\Application
======
At the end of this file, this is how your sample project directory may look like :
```
| app
| ─ app.php
| public
| ─ index.php
| vendor
| wizard
| composer.json
| composer.lock
```
#### /app/app.php
You can write up the boot file (**app.php**) anywhere. But there're several important directory paths required to be configured.

First, create the boot file named on **/app/app.php**

##### Returns the \Exedra\Application
And load the composer autoload accordingly. The **app.php** file should return the same \Exedra\Application instance, so it's usable for the front controller public/index.php, or wizard (console) later.

Construct the application with your root directory (**path.root**) as the first argument.
```
<?php
// assuming that this app.php is located under /app/
require_once __DIR__.'/../vendor/autoload.php';

$app = new \Exedra\Application(__DIR__.'/../');

return $app;
```
Or you may pass an array of paths and namespace like below :
```
<?php
// assuming that this app.php is located under /app/
require_once __DIR__.'/../vendor/autoload.php';

$app = new \Exedra\Application(array(
    'namespace' => 'App',
    'path.root' => __DIR__.'/../',
    'path.app' => __DIR__',
    'path.public' => __DIR__.'/../public/',
    'path.routes' => __DIR__
    ));

return $app;
```
These are optional and internally configured if not passed, only path.root is required. Originally it, may look something like this
```
| .        //path.root
| app      //path.app
| ─ Routes //path.routes
| public   //path.public
```

On the instantiation of the instance, it will autoload the configured **path.app** with the given namespace.

#### /app/app.php sample routing
Now, in the same **app.php** let's write some nestful chatting api codes :
```
// global middleware
$app->map->middleware(function($exe)
{
    return $exe->next($exe);
});

// or specify by class name
$app->map->middleware(\App\Middleware\All::CLASS);

$app->map->any('/api')->middleware(\App\Middleware\Api::CLASS)->group(function($api)
{
    // or inversely, you can register the middleware into the current route, through this level.
    $api->middleware(\App\Middleware\ApiAuth::CLASS);
    
    $api->any('/users')->group(function($users)
    {
        // create new user
        // POST /api/users
        $users->post('/')->execute(function($exe)
        {
            
        });
        
        // GET /api/users/[:id]
        $users->get('/[:id]')->execute(function($exe)
        {
            return $exe->param('id');
        });
    });
    
    $api->any('/channels')->group(function($channels)
    {
        // create new channel
        // POST /api/channels
        $channels->post('/')->execute(function($exe)
        {
            
        });
        
        // GET /api/channels
        $channels->get('/')->execute(function($exe)
        {
        
        });
        
        $channels->any('/[:id]')->group(function($channel)
        {
            // GET /api/channels/:id
            $channel->get('/')->execute(function()
            {
                
            });
            
            // POST /api/channels/:id/join
            $channel->post('/join')->execute(function()
            {
            
            });
        });
    });
});

return $app;
```

#### /public/index.php
Create your front controller file (**index.php**) under your public folder (**path.public**). And require the **app.php** file;
```
<?php 
$app = require_once __DIR__.'/../App/app.php';

$app->dispatch();
```

#### /wizard
Create a file named **wizard**, in your project root directory, or anywhere convenient to you. And require the **app.php** again.
```
<?php
$app = require_once __DIR__.'/App/app.php';

$app->wizard($argv);
```
Run the wizard intro
```
php wizard
```

#### Start Basic PHP Server
execute the wizard with php :
```
php wizard serve
```
and choose the serve option. it'll serve based on the **path.public** path configured.

Another Examples
======
##### Default routing
```
$app->map->addRoutes(array(
    'book' => array(
        'uri' => '/books',
        'subroutes' => array(
            'list' => array(
                'uri' => '',
                'method' => 'GET',
                'execute' => 'controller=Book@List',
            'view' => array(
                'uri' => '[:id]',
                'method' => 'GET',
                'execute' => ''controller=Book@View'
                )
            )
        )
    )
));
```
Some of the projects built on top of exedra :

http://github.com/rosengate/exedra-web (hosted at exedra.rosengate.com)

Roadmap to 0.3.0
======
- Adapt several PSR's styles and standards
  - PSR-7 Message Interfaces [DONE]
  - PSR-2 ?
- More clarity on HTTP Response [DONE]
- proper jargon renames
  - 'uri' to 'path' [DONE]
  - builder to factory [DONE]
- Internal Routing Improvements [PARTIAL]
- More type of Exceptions [PARTIAL]
- More clarity on application structure [Structure Class removed]
- Container based \Exedra\Application\Application and \Exedra\Application\Execution\Exec ? [DONE]
- Move \Exedra\Application\Execution\ and all the related namespaces outsides ? [DONE]
- Do more tests [PARTIAL]

Thank you!
======
I hope you guys like it!
