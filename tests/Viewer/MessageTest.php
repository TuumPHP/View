<?php
namespace tests\Viewer;

use Tuum\View\Helper\Message;

require_once(dirname(__DIR__) . '/autoloader.php');

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function message()
    {
        $message = Message::forge([]);
        $message->add('message', Message::MESSAGE);
        $this->assertEquals('<div class="alert alert-success">message</div>', (string)$message);
        $message->formats[Message::MESSAGE] = '<%s>';
        $this->assertEquals('<message>', (string)$message);
    }

    /**
     * @test
     */
    function multiple_messages()
    {
        $message = Message::forge([]);
        $message->add('message1', Message::MESSAGE);
        $message->add('message2', Message::MESSAGE);
        $message->add('error', Message::ERROR);
        $message->formats[Message::MESSAGE] = 'M<%s>';
        $message->formats[Message::ERROR]   = 'E<%s>';
        $this->assertEquals('M<message1>M<message2>E<error>', (string)$message);
    }

    /**
     * @test
     */
    function onlyOne_returns_only_one_most_severe_message()
    {
        $message = Message::forge([]);
        $message->add('message', Message::MESSAGE);
        $message->add('alert', Message::ALERT);
        $message->add('error', Message::ERROR);
        $message->formats[Message::ERROR]   = 'E<%s>';
        $this->assertEquals('E<error>', $message->onlyOne());
    }

    /**
     * @test
     */
    function empty_message_returns_empty_string()
    {
        $message = Message::forge([]);
        $this->assertEquals('', (string)$message);
        $this->assertEquals('', $message->onlyOne());
    }
}
