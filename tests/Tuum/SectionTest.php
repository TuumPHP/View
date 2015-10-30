<?php
namespace tests\Locator;

use Tuum\View\Locator;
use Tuum\View\Renderer;

require_once(__DIR__ . '/../autoloader.php');

class SectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Renderer
     */
    var $view;

    function setup()
    {
        class_exists(Renderer::class);
        $this->view = new Renderer(new Locator(__DIR__ . '/section-view'));
        $this->view->setLayout('layout');
    }

    /**
     * @test
     */
    function case_block_()
    {
        $view = $this->view->render('case-block');
        $this->assertEquals('#Section Layout

#Section: breadcrumb
    bread-top
    

#Section: menu
block menu

#Section: content
    This is block content.
This is sub block. ', $view);
    }

    /**
     * @test
     */
    function case_marked_no_render()
    {
        $view = $this->view->render('case-marked');
        $this->assertEquals('#Section Layout

#Section: breadcrumb


#Section: menu


#Section: content
    This is full case view. ', $view);
    }

    /**
     * @test
     */
    function case_full_returns_full_of_sections()
    {
        $view = $this->view->render('case-full');
        $this->assertEquals('#Section Layout

#Section: breadcrumb
    bread-top
    full


#Section: menu
none, full, marked


#Section: content
    This is full case view. ', $view);
    }

    /**
     * @test
     */
    function case_none_returns_layout()
    {
        $view = $this->view->render('case-none');
        $this->assertEquals('#Section Layout

#Section: breadcrumb
    bread-top
    

#Section: menu
    menu-top
    

#Section: content
    No nothing.', $view);
    }
}