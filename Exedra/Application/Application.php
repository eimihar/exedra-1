<?php
namespace Exedra\Application;

class Application
{
	private $started			= false;
	private $name				= null;
	private $executor			= null;
	public $router				= null;
	public $response			= null;
	public $structure			= null;
	private $executionFailRoute	= null;
	private $currentRoute		= null;
	private $currentExe 		= null;

	public function __construct($name,$exedra)
	{
		$this->name = $name;
		$this->exedra = $exedra;

		## register dependency.
		$this->register();
	}

	public function setExecutionFailRoute($routename)
	{
		$this->executionFailRoute	= $routename;
	}

	public function register()
	{
		$app = $this;

		$this->structure = new \Exedra\Application\Structure($this->name);
		$this->loader = new \Exedra\Application\Loader($this->structure);

		$this->di = new \Exedra\Application\DI(array(
			"request"=>$this->exedra->httpRequest,
			"map"=> function() use($app) {return new \Exedra\Application\Map\Map($app->loader);},
			"session"=> array("\Exedra\Application\Session\Session"),
			"exception"=> array("\Exedra\Application\Builder\Exception")
			),$this);
	}

	public function __get($property)
	{
		return $this->di->get($property);
	}

	## return current execution result.
	public function getResult()
	{
		return $this->currentResult;
	}

	public function getExedra()
	{
		return $this->exedra;
	}

	/*
	main application execution interface.
	*/
	public function execute($query,$parameter = Array())
	{
		try
		{
			$query	= !is_array($query)?Array("route"=>$query):$query;
			$result	= $this->map->find($query);

			if(!$result)
			{
				$q	= Array();
				foreach($query as $k=>$v) $q[]	= $k." : ".$v;

				return $this->exception->create("Route not found. Query :<br>".implode("<br>",$q));
			}

			$route		= $result['route'];
			$routename	= $result['name'];
			$parameter	= array_merge($result['parameters'],$parameter);

			## save current route result.
			$this->currentRoute	= &$result;	

			$subapp		= null;
			$binds		= Array();
			$configs	= Array();

			foreach($route as $routeName=>$routeData)
			{
				## Sub app
				$subapp	= isset($routeData['subapp'])?$routeData['subapp']:$subapp;

				## Binds
				if(isset($this->map->binds[$routeName]))
				{
					foreach($this->map->binds[$routeName] as $bindName=>$callback)
					{
						$binds[$bindName][]	= $callback;
					}
				}

				## Configs
				if(isset($this->map->config[$routeName]))
				{
					foreach($this->map->config[$routeName] as $paramName=>$val)
					{
						$configs[$paramName]	= $val;
					}
				}
			}

			## Prepare result parameter and automatically create controller and view builder.
			$context	= $this;
			$exe	= new Execution\Exec($routename, $this, $parameter, $subapp);

			## has config.
			if($configs)
				$exe->addVariable("config",$configs);

			## give exec the container;
			$exe->container	= $container = new Execution\Container(Array("app"=>$this,"exe"=>$exe));

			$this->exe	= $exe;
			$executor	= new Execution\Executor($this->controller,new Execution\Binder($binds),$this);
			$execution	= $executor->execute($route[$routename]['execute'],$exe,$container);
			$this->exe->flash->clear();
			$this->exe	= null;
			return $execution;
		}
		catch(\Exception $e)
		{
			if($this->executionFailRoute)
			{
				$failRoute = $this->executionFailRoute;

				## set this false, so that it wont loop if later this fail route doesn't exists.
				$this->executionFailRoute = false;
				return $this->execute($failRoute,Array("exception"=>$e));
			}
			else
			{
				$routeName	= $this->currentRoute['name'];
				return "<pre><hr><u>Execution Exception :</u>\nRoute : $routeName\n".$e->getMessage()."<hr>";
			}
		}
	}
}
?>