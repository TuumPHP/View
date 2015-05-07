<?php
namespace tests\Locator;

use Tuum\View\Section;

require_once(__DIR__ . '/../autoloader.php');

class SectionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Section
     */
    var $sec;

    function setup()
    {
        class_exists(Section::class);
        $this->sec = new Section;
    }
    
    function test0()
    {
        $this->assertEquals('Tuum\View\Section', get_class($this->sec));
    }

    /**
     * @test
     */
    function start_and_saves_captures_output()
    {
        $this->sec->start();
        echo 'this is a test';
        $this->sec->saveAs('test1');
        $this->assertEquals( 'this is a test', $this->sec->get('test1'));
    }

    /**
     * @test
     */
    function nested_start_and_saves_captures_output()
    {
        $this->sec->start();
        echo 'this is #1 test. ';
        $this->sec->start();
        echo 'this is #2 test. ';
        $this->sec->saveAs('test2');
        $this->sec->saveAs('test1');
        $this->assertEquals( 'this is #2 test. ', $this->sec->get('test2'));
        $this->assertEquals( 'this is #1 test. this is #2 test. ', $this->sec->get('test1'));
    }

    /**
     * @test
     */    
    function renderAs_renders_section()
    {
        ob_start();
        $this->sec->start();
        echo 'this is a test';
        $this->sec->renderAs('renderAs');
        $output = ob_get_clean();
        $this->assertEquals( 'this is a test', $output);
    }

    /**
     * @test
     */
    function renderAs_markedNone_renders_none()
    {
        ob_start();
        $this->sec->markNotToRender('renderAs');
        $this->sec->start();
        echo 'this is a test';
        $this->sec->renderAs('renderAs');
        $output = ob_get_clean();
        $this->assertEquals( '', $output);
    }

    /**
     * @test
     */
    function replaceBy_renders_section()
    {
        ob_start();
        $this->sec->start();
        echo 'this is a test';
        $this->sec->replaceBy('replaceBy');
        $output = ob_get_clean();
        $this->assertEquals( 'this is a test', $output);
    }

    /**
     * @test
     */
    function replaceBy_renders_preset_section()
    {
        $this->sec->set('replaceBy', 'replaced text');
        ob_start();
        $this->sec->start();
        echo 'this is a test';
        $this->sec->replaceBy('replaceBy');
        $output = ob_get_clean();
        $this->assertEquals( 'replaced text', $output);
    }

    /**
     * @test
     */
    function replaceBy_renders_nothing_if_markedNone()
    {
        $this->sec->markNotToRender('replaceBy');
        ob_start();
        $this->sec->start();
        echo 'this is a test';
        $this->sec->replaceBy('replaceBy');
        $output = ob_get_clean();
        $this->assertEquals( '', $output);
    }
}
