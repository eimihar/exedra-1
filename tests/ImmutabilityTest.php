<?php
class ImmutabilityTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->app = new \Exedra\Application(__DIR__);

        $this->app->provider->add(\Exedra\Support\Provider\Framework::class);
	}

	public function  testConfig()
	{
		$this->app->config->set('foo', 'bar');

		$this->app->config->set('qux', 'tux');

		$this->app->map['fooRoute']->get(false)->execute(function($exe)
		{
			$exe->config->set('foo', 'baz');
		});

		$exe = $this->app->execute('fooRoute');

		$this->assertEquals($this->app['config']->get('foo'), 'bar');

		$this->assertEquals($exe['config']->get('foo'), 'baz');

		$this->assertEquals($exe['config']->get('qux'), 'tux');
	}
}