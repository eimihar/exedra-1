<?php
class ImmutabilityTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->app = new \Exedra\Application(__DIR__);
	}

	public function  testConfig()
	{
		$this->app->config->set('foo', 'bar');

		$this->app->config->set('qux', 'tux');

		$this->app->map->get(false)->name('fooRoute')->execute(function($exe)
		{
			$exe->config->set('foo', 'baz');
		});

		$exe = $this->app->execute('fooRoute');

		$this->assertEquals($this->app['config']->get('foo'), 'bar');

		$this->assertEquals($exe['config']->get('foo'), 'baz');

		$this->assertEquals($exe['config']->get('qux'), 'tux');
	}
}