<?php
namespace Tuum\View\Values;

/**
 * Class Message
 *
 * @package Tuum\View\Values
 */
class Message
{
    const MESSAGE = 'message';
    const ALERT = 'alert';
    const ERROR = 'error';

    /**
     * @var array
     */
    protected $messages = [];

    public $formats = [
        self::MESSAGE => '<div class="alert alert-success">%s</div>',
        self::ALERT   => '<div class="alert alert-info">%s</div>',
        self::ERROR   => '<div class="alert alert-danger">%s</div>',
    ];

    /**
     * @param array $data
     */
    private function __construct($data = [])
    {
        if ($data) {
            $this->messages = $data;
        }
    }

    /**
     * @param array $data
     * @return Message
     */
    public static function forge($data)
    {
        return new self($data);
    }

    /**
     * @param string $message
     * @param string $type
     */
    public function add($message, $type = self::MESSAGE)
    {
        $this->messages[] = ['message' => $message, 'type' => $type];
    }

    /**
     * @param array $msg
     * @return string
     */
    private function show($msg)
    {
        $type   = isset($msg['type']) ? $msg['type'] : self::MESSAGE;
        $format = isset($this->formats[$type]) ? $this->formats[$type] : $this->formats[self::MESSAGE];
        return sprintf($format, $msg['message']);
    }

    /**
     * show the most severe message only once.
     *
     * @return string
     */
    public function onlyOne()
    {
        $messages = [
            self::ERROR   => false,
            self::ALERT   => false,
            self::MESSAGE => false
        ];
        foreach ($this->messages as $msg) {
            $type = isset($msg['type']) ? $msg['type'] : self::MESSAGE;
            if (!$messages[$type]) {
                // message not set yet. set the message (and ignore the rest).
                $messages[$type] = $msg;
            }
        }
        foreach($messages as $msg) {
            if($msg) {
                return $this->show($msg);
            }
        }
        return '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $html = '';
        foreach ($this->messages as $msg) {
            $html .= $this->show($msg);
        }
        return $html;
    }

}