<?php
namespace Exedra\Runtime;

/**
 * Handle list of registered execution handler
 */
class Handlers
{
	/**
	 * Application instance
	 * @var \Exedra\Application
	 */
	protected $app;

	/**
	 * Registry of execution bindable in application layer.
	 * @var array registry
	 */
	protected $registry = array();

	/**
	 * List of instantiated handlers
	 * @var array handlers
	 */
	protected $handlers = array();

	public function __construct()
	{
		$this->registerDefaultHandlers();
	}

	protected function registerDefaultHandlers()
	{
		$this->register('closure', '\Exedra\Runtime\Handler\Closure');
		
		$this->register('controller', '\Exedra\Runtime\Handler\Controller');
	}

	/**
	 * Register execution handler
	 * @param string name
	 * @param string class
	 * @return self
	 */
	public function register($name, $class)
	{
		$this->registry[$name] = $class;

		return $this;
	}

	/**
	 * Alias for register()
	 */
	public function add($name, $class)
	{
		return $this->register($name, $class);
	}

	/**
	 * Resolve a handler
	 * @param \Exedra\Runtime\Exe exe
	 * @param mixed pattern
	 * @return \Closure
	 *
	 * @throws \Exedra\Exception\InvalidArgumentException
	 * @throws \Exedra\Exception\NotFoundException
	 */
	public function resolve(\Exedra\Runtime\Exe $exe, $pattern)
	{
		foreach($this->registry as $name => $className)
		{
			$handler = isset($this->handlers[$name]) ? $this->handlers[$name] : $this->handlers[$name] = new $className($name, $exe);

			if($handler->validate($pattern) === true)
			{
				$resolve = $handler->prepare($pattern);

				if(!(is_callable($resolve)))
					throw new \Exedra\Exception\InvalidArgumentException('The resolve() method for handler ['.$name.'] must return \Closure or callable');

				return $resolve;
			}
		}

		throw new \Exedra\Exception\NotFoundException('No executional pattern handler matched. '.(is_string($pattern) ? ' ['.$pattern.']' : ''));
	}
}

?>