<?php
namespace tests\Locator;

use Tuum\View\Locator;
use Tuum\View\Renderer;

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
        $container->setRoot(__DIR__.'/config-view/tested');
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
        $viewer = $viewer->setLayout('layout/layout1');
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
        $viewer = $viewer->setLayout('layout/layout_with_block');
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
        $viewer = $viewer->setLayout('layout/layout_with_block');
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

        $testing = $viewer->service('test');
        $this->assertSame($service, $testing);

        /** @noinspection PhpUndefinedMethodInspection */
        $testing = $viewer->test;
        $this->assertSame($service, $testing);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function exception()
    {
        $this->container->render('exception-test');
    }

    /**
     * @test
     */
    function exception_does_not_change_ob_level()
    {
        $this->assertEquals(1, ob_get_level());
        try {
            $this->container->render('exception-test');
            $this->assertTrue(false);
        } catch( \Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertEquals(1, ob_get_level());
    }

    /**
     * @test
     */
    function setRoot_adds_another_view_root()
    {
        $html = $this->container->render('tested');
        $this->assertEquals('', $html);
        
        $this->container->setRoot(__DIR__.'/config-view/tested');
        $html = $this->container->render('tested');
        $this->assertEquals('tested tested', $html);
    }

    /**
     * @test
     */
    function set_extension()
    {
        $html = $this->container->render('extension');
        $this->assertEquals('', $html);

        $this->container->setFileExtension('view.php');
        $html = $this->container->render('extension');
        $this->assertEquals('tested extension', $html);
    }

    /**
     * @test
     */
    function render_with_array_sets_section()
    {
        $html = $this->container->render([
            'content' => 'tested array content',
            'test' => 'tested',
        ]);
        $this->assertEquals('tested array content', $html);
        $this->assertEquals('', $this->container->getContent());
        $this->assertEquals('', $this->container->section->get('test'));
    }
}
