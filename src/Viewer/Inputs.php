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
     * @param string $name
     * @return string|array|mixed
     */
    public function get($name)
    {
        $name = str_replace('[]', '', $name);
        parse_str($name, $levels);
        $inputs = $this->inputs;
        return $this->recurseGet($levels, $inputs);
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
        if (isset($inputs[$key])) {
            return $this->recurseGet($next, $inputs[$key]);
        }
        return null;
    }

}