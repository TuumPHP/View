<?php
namespace Tuum\View\Viewer;

class Inputs
{
    /**
     * @var array
     */
    protected $inputs = [];

    /**
     * @param array $inputs
     */
    public function __construct($inputs = [])
    {
        $this->inputs = $inputs;
    }

    /**
     * @param array $inputs
     */
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * @param string        $name
     * @param string|null   $option
     * @return array|mixed|string
     */
    public function get($name, $option=null)
    {
        $name = str_replace('[]', '', $name);
        parse_str($name, $levels);
        $inputs = $this->inputs;
        $found = $this->recurseGet($levels, $inputs);
        if(!is_null($option) && is_array($found)) {
            return in_array($option, $found) ? $option: null;
        }
        return $found;
    }

    /**
     * @param array $levels
     * @param array $inputs
     * @return mixed
     */
    protected function recurseGet($levels, $inputs)
    {
        if (!is_array($levels)) {
            return $inputs;
        }
        list($key, $next) = each($levels);
        // object accessing as property
        if (is_object($inputs) && isset($inputs->$key)) {
            return $this->recurseGet($next, $inputs->$key);
        }
        // object accessing as ArrayAccess
        if (is_object($inputs) && $inputs instanceof \ArrayAccess && isset($inputs[$key])) {
            return $this->recurseGet($next, $inputs[$key]);
        }
        // an array
        if (is_array($inputs) && isset($inputs[$key])) {
            return $this->recurseGet($next, $inputs[$key]);
        }
        return null;
    }

}