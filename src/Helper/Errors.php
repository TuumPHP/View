<?php
namespace Tuum\View\Helper;

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
    private function __construct($errors = [])
    {
        $this->errors = Inputs::forge($errors);
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
        if (!$msg) {
            return '';
        }
        return sprintf($this->format, $msg);
    }

}