<?php
namespace Exedra\Application\Factory;

/**
 * Exedra View Factory
 */

class View
{
	/**
	 * An Exception manager.
	 * @var \Exedra\Application\Factory\Exception
	 */
	protected $exceptionFactory;
	
	/**
	 * Intance of execution based loader.
	 * @var \Exedra\Loader
	 */
	protected $loader;

	/**
	 * Default datas for this view.
	 * @var array
	 */
	protected $defaultData = array();

	/**
	 * Base dir
	 * @var string|null
	 */
	protected $baseDir = null;

	/**
	 * View default extension.
	 * @var string
	 */
	protected $ext = 'php';

	public function __construct(\Exedra\Application\Factory\Exception $exceptionFactory, \Exedra\Loader $loader)
	{
		$this->loader = $loader;
		$this->exceptionFactory = $exceptionFactory;
	}

	/**
	 * Create view instance.
	 * @param string path
	 * @param array data
	 * @return \Exedra\Application\Response\View view
	 */
	public function create($path, $data = array())
	{
		$path = $this->baseDir ? $this->baseDir . '/' . ltrim($path) : $path;

		// $path = $this->buildPath($path);
		if(!$this->has($path))
			$this->exceptionFactory->create("Unable to find view '$path'");
		
		// append .php extension.
		$path = $this->buildPath($path);

		// merge with default data.
		if(count($this->defaultData) > 0)
			$data = array_merge($data, $this->defaultData);

		$view	= new Blueprint\View($this->exceptionFactory, $path, $data, $this->loader);
		
		return $view;
	}

	/**
	 * Set base dir to be concenatted at the beginning of view path.
	 * @param string dir
	 *
	 */
	public function setBaseDir($dir)
	{
		$this->baseDir = $dir;
	}

	/**
	 * Build path with extension
	 * @param string path
	 * @return string
	 */
	protected function buildPath($path)
	{
		$path	= $path. '.' .$this->ext;

		return $path;
	}

	/**
	 * Check file's path existence.
	 * @param string path
	 * @param boolean build
	 * @return boolean
	 */
	public function has($path, $build = true)
	{
		if($build)
			$path = $this->buildPath($path);

		return $this->loader->has(array('structure'=> 'view', 'path'=> $path));
	}

	/**
	 * Set default data for every view created through this factory.
	 * @param mixed name
	 * @param data string
	 * @return this
	 */
	public function setDefaultData($key, $val = null)
	{
		if(is_array($key))
		{
			foreach($key as $k=>$v)
			{
				$this->setDefaultData($k, $v);
			}
		}
		else
		{
			$this->defaultData[$key] = $val;
		}

		return $this;
	}
}