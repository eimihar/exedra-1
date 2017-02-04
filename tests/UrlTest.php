<?php
class UrlTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testUrl()
    {
        $url = new \Exedra\Url\Url('http://localhost/test');

        $this->assertEquals('http://localhost/test', $url);
    }

    public function testFactory()
    {
        $app = new \Exedra\Application(__DIR__);

        $app->map['foo']->get('/bar')->execute(function(){});

        $factory = new \Exedra\Url\UrlFactory($app->map, null, 'http://localhost');

        $this->assertEquals((string) $factory->to('hello'), 'http://localhost/hello');

        $this->assertEquals((string) $factory->to('hello')->setHost('192.168.1.100'), 'http://192.168.1.100/hello');

        $this->assertEquals($factory->route('foo')->addParam('baz', 'bat')->addParams(array('baft' => 'jazt', 'taz' => 'tux')), 'http://localhost/bar?baz=bat&baft=jazt&taz=tux');
    }

    public function testGenerator()
    {
        $app = new \Exedra\Application(__DIR__);

        $app->map['foo']->get('/fox')->execute(function(){});

        $generator = new \Exedra\Url\UrlGenerator($app->map, null, 'http://localhost');

        $this->assertEquals($generator->to('foobar'), 'http://localhost/foobar');
    }
}