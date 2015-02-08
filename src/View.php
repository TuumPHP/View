<?php
namespace Tuum\View;

use Traversable;

/**
 * Class View
 *
 * @property Errors errors
 * @property Inputs inputs
 * @property Message message
 * @package Tuum\View
 */
class View implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var Inputs
     */
    protected $inputs;

    /**
     * @var Errors
     */
    protected $errors;

    /**
     * @var array
     */
    protected $_data_ = [];

    /**
     * @ param Message $message
     */
    public function __construct()
    {
        $this->inputs = new Inputs();
        $this->errors = new Errors();
        $this->message = new Message();
    }

    /**
     * @param string $key
     * @return null|mixed
     */
    public function __get($key)
    {
        if(isset($this->$key)) {
            return $this->$key;
        }
        return array_key_exists($key, $this->_data_) ? $this->_data_[$key] : null; 
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value=null)
    {
        if(is_array($key)) {
            $this->_data_ = array_merge($this->_data_, $key);
            return;
        }
        $this->_data_[$key] = $value;
        return;
    }

    /**
     * @param string $message
     * @param bool   $error
     */
    public function setMessage($message, $error = false)
    {
        $this->message->add($message, $error);
    }

    /**
     * @param array $input
     */
    public function setInput($input)
    {
        $this->inputs->setInputs($input);
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors->setErrors($errors);
    }

    /**
     * @param mixed $offset
     * @return boolean 
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data_);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset 
     * @return mixed 
     */
    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->_data_) ? $this->_data_[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_data_[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset 
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (array_key_exists($offset, $this->_data_) ) {
            unset($this->_data_[$offset]);
        }
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable 
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_data_);
    }
}