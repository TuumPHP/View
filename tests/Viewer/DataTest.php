<?php
namespace tests\Viewer;

use Tuum\View\Helper\Data;

require_once(dirname(__DIR__) . '/autoloader.php');

class DataTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $this->assertEquals('Tuum\View\Helper\Data', get_class(new Data()));
    }

    /**
     * @test
     */
    function view_returns_data()
    {
        $data = [
            'text' => 'tested',
            'html' => '<bold>',
        ];
        $dd = new Data($data);

        // getting values
        $this->assertEquals('tested', $dd['text']);
        $this->assertEquals('tested', $dd->text);
        $this->assertEquals('&lt;bold&gt;', $dd['html']);
        $this->assertEquals('&lt;bold&gt;', $dd->html);
        $this->assertEquals('<bold>', $dd->raw('html'));

        // check existence
        $this->assertTrue($dd->offsetExists('text'));
        $this->assertFalse($dd->offsetExists('none'));
        $this->assertTrue(isset($dd['text']));
        $this->assertFalse(isset($dd['none']));

        // html escaping
        $this->assertEquals('&lt;bold&gt;', $dd->get('html'));
        $this->assertEquals(null, $dd->get('none'));

        // hidden tags
        $this->assertEquals(null, $dd->hiddenTag('none'));
        $this->assertEquals("<input type='hidden' name='text' value='tested' />", $dd->hiddenTag('text'));
        $this->assertEquals("<input type='hidden' name='html' value='&lt;bold&gt;' />", $dd->hiddenTag('html'));
    }

    /**
     * @test
     */
    function view_iteration()
    {
        $data = [
            'text' => 'tested',
            'more' => 'done',
        ];
        $dd = new Data($data);
        foreach($dd as $key => $value) {
            $this->assertEquals($data[$key], $value);
        }
    }
    /**
     * @test
     */
    function withKey_returns_new_view_object()
    {
        $d1 = new Data(['test'=>['more' => 'testing']]);
        $d2 = $d1->extractKey('test');
        $this->assertEquals(['more' => 'testing'], $d1->test);
        $this->assertEquals('testing', $d2->more);
    }

    /**
     * @test
     */
    function can_set_value_as_property()
    {
        $dd = new Data();
        $dd->test = 'tested';
        $this->assertEquals('tested', $dd->test);
        $this->assertEquals(null, $dd['test']);
    }

    /**
     * @test
     */
    function view_can_handle_object()
    {
        $obj = new \stdClass();
        $obj->test = 'tested';
        $dd = new Data($obj);
        $this->assertEquals('tested', $dd->get('test'));
    }

    /**
     * @test
     */
    function view_can_handle_arrayAccess_object()
    {
        $obj = new \ArrayObject(['test'=>'tested']);
        $dd = new Data($obj);
        $this->assertEquals('tested', $dd->get('test'));
    }
    /**
     * @test
     */
    function withKey_creates_new_view()
    {
        $obj1 = new \stdClass();
        $obj1->test = 'tested';
        $obj2 = new \stdClass();
        $obj2->test = 'done';
        $dd = new Data([
            'list' => [
                $obj1,
                $obj2
            ]
        ]);
        $list = $dd->extractKey('list');
        $this->assertEquals('Tuum\View\Helper\Data', get_class($list));

        $answer = ['tested', 'done'];
        foreach($list->getKeys() as $key) {
            $object = $list->extractKey($key);
            $this->assertEquals('Tuum\View\Helper\Data', get_class($object));
            $this->assertEquals($answer[$key], $object->test);
        }
    }


}
