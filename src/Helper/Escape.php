<?php
namespace Tuum\View\Helper;

class Escape
{
    /**
     * @var callable
     */
    private $escape = ['Tuum\View\Helper\Escape','htmlSafe'];

    /**
     * @param null|callable $escape
     */
    public function __construct($escape = null)
    {
        if (is_callable($escape)) {
            $this->escape = $escape;
        }
    }

    /**
     * escape for html output.
     *
     * @param string $string
     * @return string
     */
    public static function htmlSafe($string)
    {
        return is_string($string) ? htmlspecialchars($string, ENT_QUOTES, 'UTF-8') : $string;
    }

    /**
     * @param string $string
     * @return mixed
     */
    public function __invoke($string)
    {
        return $this->escape($string);
    }
    
    /**
     * escapes a string using $this->escape.
     *
     * @param string $string
     * @return mixed
     */
    public function escape($string)
    {
        if (is_string($string)) {
            $func = $this->escape;
            return $func($string);
        }
        return $string;
    }

    /**
     * @return callable
     */
    public function getEscape()
    {
        return function ($value) {
            return $this->escape($value);
        };
    }

    /**
     * @param callable $escape
     * @return Escape
     */
    public function withEscape($escape)
    {
        $self = clone($this);
        $self->escape = $escape;
        return $self;
    }

}