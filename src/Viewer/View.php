<?php
namespace Tuum\View\Viewer;

use Psr\Http\Message\UriInterface;
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
     * @var array
     */
    protected $_data_ = [];

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
     * @var UriInterface
     */
    public $uri;

    /**
     * @ param Message $message
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->inputs = new Inputs($this->bite($data, 'inputs'));
        $this->errors = new Errors($this->bite($data, 'errors'));
        $this->message = new Message($this->bite($data, 'messages'));
        $this->_data_ = $data;
    }

    /**
     * @param array  $data
     * @param string $key
     * @return array
     */
    private function bite(&$data, $key)
    {
        if(array_key_exists($key, $data) && is_array($data[$key])) {
            return $data[$key];
        }
        return [];
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
     * @return string
     */
    public function hiddenTag($key)
    {
        if ($this->offsetExists($key)) {
            $value = $this->offsetGet($key);
            return "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\" />";
        }
        return '';
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

    /**
     * @param UriInterface $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * get data as list (i.e. array).
     *
     * @param string $key
     * @return array
     */
    function asList($key)
    {
        if( $this->offsetExists($key)) {
            $value = $this->offsetGet($key);
            return is_array($value) ? $value : [$value];
        }
        return [];
    }
}