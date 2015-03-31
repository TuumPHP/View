<?php
namespace Tuum\View\Values;

use Traversable;

class Data
{
    /**
     * @var array|object
     */
    protected $data = [];

    /**
     * @var callable
     */
    private $escape;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @ param Message $message
     *
     * @param array|object  $data
     * @param null|callable $escape
     */
    public function __construct($data = [], $escape = null)
    {
        $this->data   = $data;
        $this->escape = $escape ?: 'Tuum\View\Value::htmlSafe';
    }

    /**
     * @param array|object  $data
     * @param null|callable $escape
     * @return Data
     */
    public static function forge($data = [], $escape = null)
    {
        return new self($data, $escape);
    }

    /**
     * returns new Data object populated with its data[$key].
     *
     * @param string $key
     * @return Data
     */
    public function extractKey($key)
    {
        $data       = $this->get($key, []);
        $view       = clone($this);
        $view->data = $data;
        return $view;
    }

    /**
     * accessing data as property. returns escaped value.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * get an escaped value.
     *
     * @param string     $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value  = $this->raw($key, $default);
        $escape = $this->escape;
        return $escape($value);
    }

    /**
     * get a raw value.
     *
     * @param string     $key
     * @param null|mixed $default
     * @return mixed
     */
    public function raw($key, $default = null)
    {
        if ((is_array($this->data) || $this->data instanceof \ArrayAccess)
            && isset($this->data[$key])
        ) {
            return $this->data[$key];
        }
        if (is_object($this->data) && isset($this->data->$key)) {
            return $this->data->$key;
        }
        return $default;
    }

    /**
     * get value as hidden tag using $key as name.
     *
     * @param string $key
     * @return string
     */
    public function hiddenTag($key)
    {
        if ($value = $this->get($key)) {
            return "<input type='hidden' name='{$key}' value='{$value}' />";
        }
        return '';
    }

    /**
     * get keys of current data (if it is an array).
     *
     * @return array
     */
    public function getKeys()
    {
        return is_array($this->data) ? array_keys($this->data) : [];
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}