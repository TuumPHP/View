<?php
namespace Tuum\View;

class Errors
{
    /**
     * @var Inputs
     */
    protected $errors;

    /**
     * @var string
     */
    public $format = '<span class="text-danger">%s</span>';
    
    /**
     * @param null|Inputs $errors
     */
    public function __construct($errors=null)
    {
        if(!$errors) {
            $errors = new Inputs();
        }
        $this->errors = $errors;
    }
    
    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors->setInputs($errors);
    }

    /**
     * @param string $name
     * @return array|mixed|string
     */
    public function get($name)
    {
        return $this->errors->get($name);
    }

    /**
     * @param $name
     * @return string
     */
    public function text($name)
    {
        $msg = $this->get($name);
        if(!$msg) return '';
        return sprintf($this->format, $msg);
    }
}