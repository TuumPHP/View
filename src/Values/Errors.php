<?php
namespace Tuum\View\Values;

class Errors
{
    /**
     * @var Inputs
     */
    protected $errors;

    /**
     * @var string
     */
    public $format = '<p class="text-danger">%s</p>';

    /**
     * @param array|Inputs $errors
     */
    private function __construct($errors=null)
    {
        if(!$errors) {
            $errors = Inputs::forge();
        }
        elseif(is_array($errors)) {
            $errors = Inputs::forge($errors);
        }
        $this->errors = $errors;
    }

    /**
     * @param array $data
     * @return Errors
     */
    public static function forge($data)
    {
        return new self($data);
    }

    /**
     * @param string $name
     * @return array|mixed|string
     */
    public function raw($name)
    {
        return $this->errors->raw($name);
    }

    /**
     * @param $name
     * @return string
     */
    public function get($name)
    {
        $msg = $this->raw($name);
        if(!$msg) return '';
        return sprintf($this->format, $msg);
    }

}