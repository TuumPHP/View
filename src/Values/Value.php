<?php
namespace Tuum\View\Values;

use Psr\Http\Message\UriInterface;

/**
 * Class Value
 *
 * @package Tuum\View\Values
 *
 * @property Errors           $errors
 * @property Inputs           $inputs
 * @property Message          $message
 * @property Data             $data
 * @property UriInterface     $uri
 */
class Value
{
    const MESSAGE = '-message-view';
    const INPUTS = '-input-view';
    const ERRORS = '-errors-view';
    const URI = '-uri-view';

    /**
     * @var Data
     */
    private $data;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var Inputs
     */
    private $inputs;

    /**
     * @var Errors
     */
    private $errors;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var callable
     */
    private $escape = ['Tuum\View\Values\Value','htmlSafe'];

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
     * @param array $data
     * @return $this
     */
    public function forge($data)
    {
        $blank = clone($this);
        $blank->setup($data);
        return $blank;
    }

    /**
     * @param array $data
     */
    private function setup($data)
    {
        /**
         * @param string $key
         * @return array
         */
        $bite = function ($key = null) use ($data) {
            if (is_null($key)) {
                return $data;
            }
            if (array_key_exists($key, $data)) {
                $found = $data[$key];
                unset($data[$key]);
                return $found;
            }
            return [];
        };

        $this->inputs  = Inputs::forge($bite(self::INPUTS), $this->getEscape());
        $this->errors  = Errors::forge($bite(self::ERRORS));
        $this->message = Message::forge($bite(self::MESSAGE));
        $this->uri     = $bite(self::URI);
        $this->data    = Data::forge($bite(), $this->getEscape());
    }

    /**
     * escape for html output.
     *
     * @param string $string
     * @return string
     */
    public static function htmlSafe($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    // +----------------------------------------------------------------------+
    //  values from current data.
    // +----------------------------------------------------------------------+
    /**
     * accessing its internal properties.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }
        return null;
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
     */
    public function setEscape($escape)
    {
        $this->escape = $escape;
    }
}