<?php
namespace tests\Locator;

use Tuum\Locator\Locator;
use Tuum\View\Tuum\Renderer;

require_once(__DIR__ . '/../autoloader.php');

class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Renderer
     */
    var $container;
    
    function setup()
    {
        class_exists(Renderer::class);
        $this->container = new Renderer(new Locator(__DIR__.'/config-view'));
    }

    /**
     * @test
     */
    function locator_reads_files_under_config()
    {
        $container = $this->container;
        $this->assertEquals('test', $container->render('test'));
        $this->assertEquals('more', $container->render('more'));
        $this->assertEquals(null, $container->render('tested'));
    }

    /**
     * @test
     */
    function locator_reads_files_under_config_and_tested()
    {
        $container = $this->container;
        $container->locator->addRoot(__DIR__.'/config-view/tested');
        $this->assertEquals('tested test', $container->render('test'));
        $this->assertEquals('more', $container->render('more'));
        $this->assertEquals('tested tested', $container->render('tested'));
    }

    /**
     * @test
     */
    function layout()
    {
        $viewer = $this->container;
        $viewer = $viewer->withView('layout/layout1');
        $output = $viewer->render('test');
        $this->assertEquals("This is layout#1.\n\ntest\n\nEnd of layout#1.", $output);
    }

    /**
     * @test
     */
    function set_layout_inside_a_view()
    {
        $viewer = $this->container;
        $output = $viewer->render('set_layout_inside_view');
        $this->assertEquals("This is layout#1.\n\nset layout inside this view.\n\nEnd of layout#1.", $output);
    }

    /**
     * @test
     */
    function layout_with_block()
    {
        $viewer = $this->container;
        $viewer = $viewer->withView('layout/layout_with_block');
        $output = $viewer->render('content_with_section');
        $this->assertEquals("This is layout-with-block.\n\nBlock:\nthis is a block.\n\nContent:\nthis is a content.", $output);
    }

    /**
     * @test
     */
    function setting_layout_inside_view_overwrites_default_layout()
    {
        $viewer = $this->container;
        $output = $viewer->render('set_layout_inside_view');

        $viewer = $this->container;
        $viewer = $viewer->withView('layout/layout_with_block');
        $this->assertEquals($output, $viewer->render('set_layout_inside_view'));
    }

    /**
     * @test
     */    
    function register_service_and_retrieve_it()
    {
        $service = new \stdClass();
        $service->tested = 'done';
        
        $viewer = $this->container;
        $viewer->register('test', $service);

        /** @noinspection PhpUndefinedMethodInspection */
        $testing = $viewer->test();
        $this->assertSame($service, $testing);
    }
}
