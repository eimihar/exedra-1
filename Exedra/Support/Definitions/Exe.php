<?php
namespace Exedra\Support\Definitions;

use Exedra\Config;
use Exedra\Container\Registry;
use Exedra\Factory\Controller;
use Exedra\Http\ServerRequest;
use Exedra\Path;
use Exedra\Routing\Finding;
use Exedra\Routing\Route;
use Exedra\Runtime\Redirect;
use Exedra\Runtime\Response;
use Exedra\Session\Flash;
use Exedra\Session\Session;
use Exedra\Support\Runtime\ControllerFactory;
use Exedra\Support\Runtime\Form\Form;
use Exedra\Support\Runtime\Url\UrlFactory;
use Exedra\View\Factory;

/**
 * Properties Definitions Exe container
 * Interface Exe
 * @package Exedra\Support\Definitions
 *
 * @property Application $app
 * @property Path $path
 * @property Config $config
 * @property UrlFactory $url
 * @property Redirect $redirect
 * @property Form $form
 * @property Factory $view
 * @property Registry[] $services
 * @property Session $session
 * @property Flash $flash
 * @property ControllerFactory $controller
 * @property ServerRequest $request
 * @property Response $response
 * @property Finding $finding
 * @property Route $route
 */
interface Exe
{

}