<?php
namespace Exedra\Form;

class Form
{
    /**
     * Form data
     * @var array data
     */
    protected $data = array();

    /**
     * Form options data
     * For select() for example
     * @var array dataOptions
     */
    protected $dataOptions = array();

    /**
     * Overriding form data
     * @var array override
     */
    protected $override = array();

    /**
     * Initialize given data
     * @param array $data
     */
    public function initialize(array $data = array())
    {
        $this->set($data);
    }

    /**
     * Set form data
     * @param string|array key
     * @param string|boolean value
     * @param boolean $override
     * @return $this
     */
    public function set($key, $value = null, $override = false)
    {
        // if key is array,
        // loop the set, while expecting value parameter to be override
        if(is_array($key))
        {
            $override = $value;

            foreach($key as $k => $val)
            {
                if($override === true)
                    $this->setOverride($k, $val);
                else
                    $this->data[$k] = $val;
            }

            return $this;
        }
        else
        {
            if($override === true)
                $this->setOverride($key, $value);
            else
                $this->data[$key] = $value;

            return $this;
        }
    }

    /**
     * Set overriding data
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setOverride($key, $value = null)
    {
        if(is_array($key))
            foreach($key as $k => $val)
                $this->override[$k] = $val;
        else
            $this->override[$key] = $value;

        return $this;
    }

    /**
     * Set options
     * @param string $key
     * @param array $options
     * @return $this
     */
    public function setOptions($key, array $options)
    {
        $this->dataOptions[$key] = $options;

        return $this;
    }

    /**
     * Alias to setOptions
     */
    public function setOption($key, array $options)
    {
        return $this->setOptions($key, $options);
    }

    /**
     * Alias to setOverride
     */
    public function override($key, $value = null)
    {
        return $this->setOverride($key, $value);
    }

    /**
     * Alias to set()
     */
    public function populate($key, $value = null, $override = false)
    {
        return $this->set($key, $value, $override);
    }

    /**
     * Check data existence
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->override[$key]) ? true : (isset($this->data[$key]) ? true : false);
    }

    /**
     * Get form data
     * @param string $key
     * @param string|null $default
     * @return string
     */
    public function get($key, $default = null)
    {
        return isset($this->override[$key]) ? $this->override[$key] : (isset($this->data[$key]) ? $this->data[$key] : $default);
    }

    /**
     * Create html input
     * @param string $type
     * @param string $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    protected function createInput($type, $name = null, $value = null, $attr = null)
    {
        if($type == 'textarea')
            $input = new Input\Textarea($name);
        else
            $input = new Input\Input($type, $name);

        if($name)
            $input->attr('id', $name);

        if($value)
            $input->value($value);
        else if(isset($this->data[$name]))
            $input->value($this->data[$name]);

        if($attr)
            $input->attr($attr);

        if(isset($this->override[$name]))
            $input->override($this->override[$name]);

        return $input;
    }

    /**
     * Create select input
     * @param string|null $name
     * @param array $options
     * @param string|null $value
     * @param array|string|null $attr
     * @param mixed|null $first
     * @return Input\Select
     */
    public function select($name = null, array $options = array(), $value = null, $attr = null, $first = null)
    {
        $select = new Input\Select($name);

        if($name)
            $select->attr('id', $name);

        if(count($options) > 0)
            $select->options($options);
        elseif (isset($this->dataOptions[$name]))
            $select->options($this->dataOptions[$name]);

        if($value)
            $select->value($value);
        else if(isset($this->data[$name]))
            $select->value($this->data[$name]);

        if($attr)
            $select->attr($attr);

        if(isset($this->override[$name]))
            $select->override($this->override[$name]);

        if($first)
            $select->first($first);

        return $select;
    }

    /**
     * Create html text input
     * @param string|null $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function text($name = null, $value = null, $attr = null)
    {
        return $this->createInput('text', $name, $value, $attr);
    }

    /**
     * Create html password input
     * @param string|null $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function password($name = null, $value = null, $attr = null)
    {
        return $this->createInput('password', $name, $value, $attr);
    }

    /**
     * Create html textarea input
     * @param string|null $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function textarea($name = null, $value = null, $attr = null)
    {
        return $this->createInput('textarea', $name, $value, $attr);
    }

    /**
     * Create html 5 date input
     * @param string|null $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function date($name = null, $value = null, $attr = null)
    {
        return $this->createInput('date', $name, $value, $attr);
    }

    /**
     * Create html 5 time input
     * @param string|null $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function time($name = null, $value = null, $attr = null)
    {
        return $this->createInput('time', $name, $value, $attr);
    }

    /**
     * Create html file input
     * @param string|null $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function file($name = null, $value = null, $attr = null)
    {
        return $this->createInput('file', $name, $value, $attr);
    }

    /**
     * Create html hidden input
     * @param string|null $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function hidden($name = null, $value = null, $attr = null)
    {
        return $this->createInput('hidden', $name, $value, $attr);
    }

    /**
     * Create html checkbox input
     * @param string|null $name
     * @param string|null $value
     * @param bool $status
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function checkbox($name = null, $value = null, $status = false, $attr = null)
    {
        $input = $this->createInput('checkbox', $name, $value, $attr);

        if($status)
            $input->attr('checked', true);

        return $input;
    }

    /**
     * Create html submit input
     * @param string|null $name
     * @param string|null $value
     * @param array|string|null $attr
     * @return Input\Input
     */
    public function submit($name = null, $value = null, $attr = null)
    {
        return $this->createInput('submit', $name, $value, $attr);
    }
}
