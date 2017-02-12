<?php
namespace Exedra\Routing;

use Psr\Http\Message\ServerRequestInterface;

class Finding
{
	/**
	 * Found route
	 * @var \Exedra\Routing\Route $route
	 */
	public $route;

	/**
	 * List of found middlewares
	 * @var array $middlewares
	 */
	public $middlewares = array();

	/**
	 * Finding attributes
	 * @var array $attributes
	 */
	protected $attributes = array();

	/**
	 * Route parameters
	 * @var array $parameters
	 */
	public $parameters = array();

	/**
	 * string of module name.
	 * @var string|null
	 */
	protected $module = null;
	
	/**
	 * string of route base
	 * @var string|null $baseRoute
	 */
	protected $baseRoute = null;

	/**
	 * Request instance
	 * Since exedra didn't implement it
	 * It'll not be typehinted
	 * @var \Psr\Http\Message\RequestInterface|null $request
	 */
	protected $request = null;

	/**
	 * @var array config
	 */
	protected $config = array();

	/**
	 * Stacked handlers
	 * @var array handlers
	 */
	protected $handlers = array();

	/**
	 * @param \Exedra\Routing\Route|null
	 * @param array $parameters
     * @param mixed $request
	 */
	public function __construct(\Exedra\Routing\Route $route = null, array $parameters = array(), ServerRequestInterface $request = null)
	{
		$this->route = $route;

		$this->request = $request;

		if($route)
		{
			$this->addParameters($parameters);

			$this->resolve();
		}
	}

	/**
	 * Get route 
	 * @return \Exedra\Routing\Route|null
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * Append given parameters
	 * @param array $parameters
	 */
	public function addParameters(array $parameters)
	{
		foreach($parameters as $key => $param)
			$this->parameters[$key] = $param;
	}

    /**
     * Get findings parameter
     * Return all if no argument passed
     * @param string|null $name
     * @return array|mixed
     */
	public function param($name = null)
	{
		if($name === null)
			return $this->parameters;

		return $this->parameters[$name];
	}

	/**
	 * Whether finding is successful
	 * @return boolean
	 */
	public function isSuccess()
	{
		return $this->route ? true : false;
	}

	/**
	 * Resolve finding informations
	 * resolve, baseRoute, middlewares, config, attributes
	 */
	public function resolve()
	{
		$this->baseRoute = null;

		foreach($this->route->getFullRoutes() as $route)
		{
			// get the latest module and route base
			if($route->hasProperty('module'))
				$this->module = $route->getProperty('module');

			// stack all the handlers
			foreach($route->getGroup()->getExecuteHandlers() as $name => $handler)
				$this->handlers[$name] = $handler;

			// if has parameter base, and it's true, set base route to the current route.
			if($route->hasProperty('base') && $route->getProperty('base') === true)
				$this->baseRoute = $route->getAbsoluteName();

			foreach($route->getGroup()->getMiddlewares() as $key => $middleware)
            {
                if(is_string($key))
                    $this->middlewares[$key] = $middleware;
                else
                    $this->middlewares[] = $middleware;
            }

			// append all route middlewares
			foreach($route->getProperty('middleware') as $key => $middleware)
            {
                if(is_string($key))
                    $this->middlewares[$key] = $middleware;
                else
                    $this->middlewares[] = $middleware;
            }

			foreach($route->getAttributes() as $key => $value)
				$this->attributes[$key] = $value;

			// pass conig.
			if($route->hasProperty('config'))
				$this->config = array_merge($this->config, $route->getProperty('config'));
		}
	}

	/**
	 * Check has middlewares or not
	 * @return boolean
	 */
	public function hasMiddlewares()
	{
		return count($this->middlewares) > 0;
	}

	/**
	 * @return array
	 */
	public function getMiddlewares()
	{
		return $this->middlewares;
	}

    /**
     * @param $key
     * @return $this
     */
    public function removeMiddleware($key)
    {
        unset($this->middlewares[$key]);

        return $this;
    }

	/**
	 * Get stacked handlers
	 * @return array
	 */
	public function getHandlers()
	{
		return $this->handlers;
	}

	/**
	 * An array of config of the finding
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * Get route found attribute
	 * @param string $key
	 * @param mixed $default value
	 * @return mixed
	 */
	public function getAttribute($key, $default = null)
	{
		return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : $default;
	}

	/**
	 * Check whether attribute exists
	 * @param string $key
	 * @return boolean
	 */
	public function hasAttribute($key)
	{
		return array_key_exists($key, $this->attributes);
	}

	/**
	 * Module on this finding.
	 * @return string referenced module name
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Get base route configured for this Finding.
	 * @return string
	 */
	public function getBaseRoute()
	{
		return $this->baseRoute;
	}

	/**
	 * Get Http request found along with the finding
	 * @return ServerRequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}
}