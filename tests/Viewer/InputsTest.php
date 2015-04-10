<?php
namespace tests\Viewer;

use Tuum\View\Helper\Inputs;

class InputsTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $this->assertEquals('Tuum\View\Helper\Inputs', get_class(Inputs::forge()));
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
        $input = Inputs::forge($data);
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
            ],
            'exact' => 'value',
        ];
        $input = Inputs::forge($data);
        $this->assertEquals($data['test'], $input->get('test'));
        $this->assertEquals(true, $input->exists('test', 'assured'));
        $this->assertEquals(false, $input->exists('test', 'bad'));
        $this->assertEquals(true, $input->exists('exact', 'value'));

        $this->assertEquals(' checked',  $input->checked('test', 'assured'));
        $this->assertEquals(' selected', $input->selected('test', 'assured'));
        $this->assertEquals('',  $input->checked('test', 'bad'));
        $this->assertEquals('', $input->selected('test', 'bad'));
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
        $input = Inputs::forge($data);
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
        $input = Inputs::forge($data);
        $this->assertEquals($data['test'], $input->get('test'));
        $this->assertEquals('arrayObject', $input->get('test[name]'));
        $this->assertEquals(null, $input->get('test[bad]'));
    }

    /**
     * @test
     */
    function inputs_returns_empty_string()
    {
        $data  = [
            'test' => [
                'null' => null,
                'false' => false,
                'empty' => '',
                'zero' => '0',
                'true' => true,
                'test' => 'tested',
            ]
        ];
        $input = Inputs::forge($data);
        $this->assertTrue('' === $input->get('test[null]'));
        $this->assertTrue('' === $input->get('test[false]'));
        $this->assertTrue('' === $input->get('test[empty]'));
        $this->assertTrue('0' === $input->get('test[zero]'));
        $this->assertTrue(true === $input->get('test[true]'));
        $this->assertEquals('tested', $input->get('test[test]'));
    }

    /**
     * @test
     */
    function inputs_in_readme_example()
    {
        $data  = [
            'name' => '<my> name',
            'gender' => 'male',
            'types' => [ 'a', 'c' ],
            'sns' => [
                'twitter' => 'example@twitter.com',
                'facebook' => 'example@facebook.com',
            ],
        ];
        $input = Inputs::forge($data);

        $this->assertEquals('&lt;my&gt; name', $input->get('name'));
        $this->assertEquals(' checked', $input->checked('gender', 'male'));
        $this->assertEquals('', $input->checked('gender', 'female'));
        $this->assertEquals(['a','c'], $input->get('types'));
        $this->assertEquals(' checked', $input->checked('types', 'a'));
        $this->assertEquals('', $input->checked('types', 'b'));
        $this->assertEquals('example@twitter.com', $input->get('sns[twitter]'));
    }
}
