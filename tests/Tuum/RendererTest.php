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
}
