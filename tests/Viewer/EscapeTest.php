<?php
namespace tests\Viewer;

use Tuum\View\Helper\Escape;

require_once(dirname(__DIR__) . '/autoloader.php');

class EscapeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function safe_escapes_and_htmlSafe_as_default()
    {
        $view = new Escape();
        $this->assertEquals('&lt;bold&gt;', $view->escape('<bold>'));
        $this->assertEquals('a&#039;b', $view->escape('a\'b'));

        // change escape functions
        $view = $view->withEscape('addslashes');
        $this->assertEquals('<bold>', $view->escape('<bold>'));
        $this->assertEquals('a\\\'b', $view->escape('a\'b'));
    }

}
