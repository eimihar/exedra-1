<?php
namespace Exedra\HTTP;

/**
 * A placeholder for upcoming PSR-7 impementation
 * implements Psr\Http\Message\MessageInterface
 */
class Message
{
	protected $protocol = '1.1';

	/**
	 * A case lowered key headers.
	 * A supposed copies for headerLines
	 * @var array headers
	 */
	protected $headers = array();

	/**
	 * Headers that store original key case
	 * @var array headerLines
	 */
	protected $headerLines = array();

	/**
	 * Stream
	 * @param \Exedra\HTTP\Stream
	 */
	protected $body;

	public function __construct(array $headers, Stream $body, $protocol = '1.1')
	{
		$this->headers = $headers;

		$this->body = $body;

		$this->protocol = $protocol;
	}

	public function __clone()
	{
		$this->body = clone $this->body;
	}

	/**
	 * @param string name
	 * @return string
	 */
	public static function headerCase($name)
	{
		return str_replace(' ', '-', ucwords(str_replace('-', ' ', strtolower($name))));
	}

	public function getProtocolVersion()
	{
		return $this->protocol;
	}

	public function setProtocolVersion($version)
	{
		$this->protocol = $version;

		return $this;
	}

	public function withProtocolVersion($version)
	{
		$message = clone $this;

		return $message->setProtocolVersion($version);
	}

	/**
	 * Get all headers
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * @param string name
	 * @return array
	 */
	public function getHeader($header)
	{
		foreach($this->headers as $key => $value)
			if(strtolower($header) == strtolower($key))
				return $value;

		return array();
	}

	/**
	 * Get header value
	 * @param name of header
	 * @return string of header value
	 */
	public function getHeaderLine($name)
	{
		return implode(', ', $this->getHeader($name));
	}

	public function withHeader($name, $value)
	{
		$message = clone $this;

		return $message->setHeader($name, $value);
	}

	public function withAddedHeader($name)
	{
		if(!$this->hasHeader($name))
			return $this->withHeader($name, $value);

		$message = clone $this;

		return $message->addHeader($name);
	}

	public function withoutHeader($name)
	{
		$message = clone $this;

		if(!$this->hasHeader($name))
			return $message;

		return $message->removeHeader($name);
	}

	/**
	 * @return \Exedra\HTTP\Stream
	 */
	public function getBody()
	{
		return $this->body;
	}

	public function setBody($body, $mode = 'r+')
	{
		switch(gettype($body))
		{
			case 'string':
				$this->body = Stream::createFromContents($body);
			break;
			case 'object':
				if($body instanceof Stream)
					$this->body = $body;
				else
					$this->body = new Stream($body, $mode);
			break;
		}

		// $this->body = $body;

		return $this;
	}

	public function withBody(Stream $body)
	{
		$message = clone $this;

		return $message->setBody($body, 'r+');
	}

	public function hasHeader($name)
	{
		return isset($this->headers[self::headerCase($name)]);
	}

	public function headerHas($name, $value)
	{
		$name = self::headerCase($name);

		if(!isset($this->headers[$name]))
			return false;

		return in_array($value, $this->headers[$name]);
	}

	public function clearHeaders()
	{
		$this->headers = array();
		$this->headerLines = array();
	}

	public function setHeaders(array $headerLines)
	{
		foreach($headerLines as $header => $values)
			$this->headers[strtolower($header)] = $values;

		$this->headerLines = $headerLines;
	}

	public function setHeader($header, $value)
	{
		$value = !is_array($value) ? array($value) : array_map('trim', $value);

		$name = strtolower($header);

		$this->headers[$name] = $value;

		foreach(array_keys($this->headerLines) as $key)
			if(strtolower($key) == $name)
				$this->headerLines[$key] = $value;
	}

	public function addHeader($header, $value)
	{
		$name = strtolower($header);

		if(is_array($value))
		{
			foreach($value as $v)
				$this->headers[$name][] = trim($v);

			foreach(array_keys($this->headerLines) as $key)
				if(strtolower($key) == $name)
					foreach($value as $v)
						$this->headerLines[$key][] = trim($v);
		}
		else
		{
			$this->headers[$name][] = trim($value);

			foreach(array_keys($this->headerLines) as $key)
				if(strtolower($key) == $name)
					$this->headerLines[$key][] = trim($v);
		}

		return $this;
	}

	public function removeHeader($header)
	{
		$name = strtolower($header);
		unset($this->headers[$name]);

		foreach($this->headerLines as $key => $value)
			if(strtolower($key) == $name)
				unset($this->headerLines[$key]);

		return $this;
	}
}