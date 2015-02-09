<?php
namespace Tuum\View\Viewer;

class Message
{
    const MESSAGE = 'message';
    const ALERT   = 'alert';
    const ERROR   = 'error';

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
    public function __construct($data = [])
    {
        if ($data) {
            $this->messages = $data;
        }
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
    public function show($msg)
    {
        $type   = isset($msg['type']) ? $msg['type'] : self::MESSAGE;
        $format = isset($this->formats[$type]) ? $this->formats[$type] : $this->formats[self::MESSAGE];
        return sprintf($format, $msg['message']);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->messages;
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