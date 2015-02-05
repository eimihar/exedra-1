<?php
namespace Exedra\Application\Response;

class Redirect
{
	public function __construct(\Exedra\Application\Execution\Exec $exe)
	{
		$this->exe = $exe;
	}

	final public function toUrl($url)
	{
		return $this->exe->response->redirect($url);
	}

	public function flash($key, $val = null)
	{
		$this->exe->flash->set($key, $val);
		return $this;
	}

	final public function refresh()
	{
		return $this->to($this->exe->getRoute(), $this->exe->getParams());
	}

	final public function to($route = null, $params = array())
	{
		if(!$route)
			return $this->refresh();

		$url = $this->exe->url->create($route, $params);

		return $this->toUrl($url);
	}
}