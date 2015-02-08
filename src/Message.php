<?php
namespace Tuum\View;

class Message
{
    /**
     * @var array
     */
    protected $messages = [];

    public $success_class = 'alert-success';
    
    public $danger_class = 'alert-danger';
    
    public $format = '<div class=\"alert %s\">
            %s
        </div>';
    /**
     * @param string $message
     * @param bool   $error
     */
    public function add($message, $error = false)
    {
        $this->messages[] = ['message' => $message, 'error' => $error];
    }

    /**
     * @param array $msg
     * @return string
     */
    public function show($msg)
    {
        $class = $msg['error'] ? $this->danger_class : $this->success_class;
        return sprintf($this->format, $class, $msg['message']);
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