<?php
namespace tests\Viewer;

use Tuum\View\Viewer\Inputs;

class InputsTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $this->assertEquals('Tuum\View\Viewer\Inputs', get_class(new Inputs()));
    }

    /**
     * @test
     */
    function input_as_array_finds_values()
    {
        $data  = [
            'test' => 'tested',
            'more' => [
                'quality' => 'assured'
            ]
        ];
        $input = new Inputs();
        $input->setInputs($data);
        $this->assertEquals('tested', $input->get('test'));
        $this->assertEquals(['quality' => 'assured'], $input->get('more'));
        $this->assertEquals('assured', $input->get('more[quality]'));
        $this->assertEquals(null, $input->get('bad'));
        $this->assertEquals(null, $input->get('bad[worse]'));
    }

    /**
     * @test
     */
    function input_like_checkbox()
    {
        $data  = [
            'test' => [
                'quality',
                'assured',
            ]
        ];
        $input = new Inputs($data);
        $this->assertEquals($data['test'], $input->get('test'));
        $this->assertEquals('assured', $input->get('test', 'assured'));
        $this->assertEquals(null, $input->get('test', 'bad'));
    }

    /**
     * @test
     */
    function input_with_array_access()
    {
        $object       = new \stdClass();
        $object->name = 'stdClass';
        $object->type = 'object';

        $data  = ['test' => $object];
        $input = new Inputs($data);
        $this->assertEquals($data['test'], $input->get('test'));
        $this->assertEquals('stdClass', $input->get('test[name]'));
        $this->assertEquals(null, $input->get('test[bad]'));
    }

    /**
     * @test
     */
    function input_as_array_access_object()
    {
        $object = new \ArrayObject();
        $object['name'] = 'arrayObject';
        $object['type'] = 'object';
        $data  = ['test' => $object];
        $input = new Inputs($data);
        $this->assertEquals($data['test'], $input->get('test'));
        $this->assertEquals('arrayObject', $input->get('test[name]'));
        $this->assertEquals(null, $input->get('test[bad]'));
    }
}
