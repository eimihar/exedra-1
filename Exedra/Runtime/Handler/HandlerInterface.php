<?php
namespace Exedra\Runtime\Handler;

interface HandlerInterface
{
	public function __construct($name, \Exedra\Runtime\Exec $exe);

	/**
	 * Validate given handler pattern
	 * @param mixed pattern
	 * @return boolean
	 */
	public function validate($pattern);

	/**
	 * Resolve into Closure or callable
	 * @return \Closure|callable
	 */
	public function resolve($pattern);
}