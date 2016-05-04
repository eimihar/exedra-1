<?php
namespace Exedra\Container;

class Registry implements \ArrayAccess
{
	protected $data = array();

	protected $name;

	public function __construct(array $registry = array())
	{
		$this->data = $registry;
	}

	/**
	 * Get registry name
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Define dependency information
	 * @param string name
	 * @param mixed definition
	 */
	public function offsetSet($name, $definition)
	{
		return $this->data[$name] = $definition;
	}

	/**
	 * Get dependency information
	 * @param string name
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		return $this->data[$name];
	}

	/**
	 * Check dependepency registry existence
	 * @param string name
	 * @return bool
	 */
	public function offsetExists($name)
	{
		return isset($this->data[$name]);
	}

	/**
	 * Remove dependency registry information
	 * @param string name
	 */
	public function offsetUnset($name)
	{
		unset($this->data[$name]);
	}

	/**
	 * Register list of dependency
	 * @param array registry
	 */
	public function register(array $registry)
	{
		$this->data = $registry;
	}

	/**
	 * Get dependency information
	 * @param string name
	 */
	public function get($name)
	{
		return $this->data[$name];
	}

	/**
	 * Register new dependency.
	 * Throw exception if already the dependency already exists.
	 * @param string name
	 * @param mixed \Closure|string|array|object
	 *
	 * @throws \Exedra\Exception\Exception
	 */
	public function add($name, $pattern)
	{
		if(isset($this->data[$name]))
			throw new \Exedra\Exception\Exception('Registry by name ['.$name.'] already exist.');
			
		$this->data[$name] = $pattern;
	}

	/**
	 * Set dependency registry
	 * @param string name
	 * @param \Closure|string|array|object
	 */
	public function set($name, $pattern)
	{
		$this->data[$name] = $pattern;
	}

	/**
	 * Check dependency information existence
	 * @param string name
	 * @return bool
	 */
	public function has($name)
	{
		return isset($this->data[$name]);
	}

	/**
	 * Remove dependency information
	 * @param string name
	 */
	public function remove($name)
	{
		unset($this->data[$name]);
	}

	/**
	 * Clear registry
	 */
	public function clear()
	{
		$this->data = array();
	}
}


?>