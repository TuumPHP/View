<?php
namespace Tuum\View\Helper;

class Inputs
{
    /**
     * @var array
     */
    private $inputs = [];

    /**
     * @var callable
     */
    private $escape;

    /**
     * @var self
     */
    private static $self;

    /**
     * private constructor. use forge method.
     */
    private function __construct()
    {
    }

    /**
     * @param array         $data
     * @param null|callable $escape
     * @return Inputs
     */
    public static function forge($data = [], $escape = null)
    {
        if (!static::$self) {
            static::$self = new static();
        }
        $blank         = clone(static::$self);
        $blank->inputs = $data;
        $blank->escape = $escape ?: ['Tuum\View\Helper\Escape', 'htmlSafe'];
        return $blank;
    }

    /**
     * get the escaped value from Inputs.
     * same as raw but the value will be escaped.
     *
     * @param string $name
     * @param mixed  $option
     * @return mixed
     */
    public function get($name, $option = null)
    {
        $found  = $this->raw($name, $option);
        $escape = $this->escape;
        return $escape($found);
    }

    /**
     * finds a value from a value from an array like $_POST
     * using form name like 'name[more]'.
     *
     * ignores the [], and returns an array of values.
     *
     * @param string      $name
     * @param string|null $option
     * @return array|mixed|string
     */
    public function raw($name, $option = null)
    {
        $name = str_replace('[]', '', $name);
        parse_str($name, $levels);
        $inputs = $this->inputs;
        $found  = $this->recurseGet($levels, $inputs);
        if (!is_null($found)) {
            return $found;
        }
        return $option;
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function exists($name, $value = null)
    {
        $found = $this->raw($name);
        if (is_null($found)) {
            return false;
        }
        if (!is_null($value)) {
            if (is_array($found)) {
                return in_array($value, $found);
            }
            return (string)$value === (string)$found;
        }
        return true;
    }

    /**
     * @param string $name
     * @param string $value
     * @return string
     */
    public function checked($name, $value)
    {
        if ($this->exists($name, $value)) {
            return ' checked';
        }
        return '';
    }

    /**
     * @param string $name
     * @param string $value
     * @return string
     */
    public function selected($name, $value)
    {
        if ($this->exists($name, $value)) {
            return ' selected';
        }
        return '';
    }

    /**
     * gets a value from $input array.
     *
     * returns null if not found. or
     * returns an empty space if key is set but has not real value.
     *
     * @param array $levels
     * @param array $inputs
     * @return mixed
     */
    private function recurseGet($levels, $inputs)
    {
        if (!is_array($levels)) {
            if (is_null($inputs) || $inputs === false) {
                $inputs = '';
            }
            return $inputs;
        }
        list($key, $next) = each($levels);
        // an array
        if (is_array($inputs) && array_key_exists($key, $inputs)) {
            return $this->recurseGet($next, $inputs[$key]);
        }
        // object accessing as ArrayAccess
        if (is_object($inputs) && $inputs instanceof \ArrayAccess && isset($inputs[$key])) {
            return $this->recurseGet($next, $inputs[$key]);
        }
        // object accessing as property
        if (is_object($inputs) && isset($inputs->$key)) {
            return $this->recurseGet($next, $inputs->$key);
        }
        return null;
    }
}