<?php
namespace Tuum\View;

use Tuum\View\Helper\Data;
use Tuum\View\Helper\Errors;
use Tuum\View\Helper\Escape;
use Tuum\View\Helper\Inputs;
use Tuum\View\Helper\Message;

/**
 * Class Value
 *
 * @package Tuum\View\DataView
 *
 */
class DataView
{
    /**
     * @var Data
     */
    public $data;

    /**
     * @var Message
     */
    public $message;

    /**
     * @var Inputs
     */
    public $inputs;

    /**
     * @var Errors
     */
    public $errors;

    /**
     * @var callable|Escape
     */
    public $escape;

    /**
     * @param null|callable $escape
     */
    public function __construct($escape = null)
    {
        if (is_callable($escape)) {
            $this->escape = $escape;
        } else {
            $this->escape = new Escape();
        }
    }
}