<?php
namespace Tuum\View\Aura;

use Aura\View\TemplateRegistry;
use Aura\View\View;
use Aura\View\ViewFactory;
use Tuum\Locator\Locator;
use Tuum\Locator\LocatorInterface;
use Tuum\View\ViewEngineInterface;

class Renderer implements ViewEngineInterface
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @param LocatorInterface $locator
     * @param View             $view
     */
    public function __construct($locator, $view)
    {
        $this->locator = $locator;
        $this->view = $view;
    }

    /**
     * @param string $view
     * @return Renderer
     */
    public static function forge($view)
    {
        $locator = new Locator($view);
        $view_factory = new ViewFactory();
        $view = $view_factory->newInstance();
        return new self($locator, $view);
    }

    /**
     * returns Aura's View object to manage anything.
     *
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * set up registry, a layout template in Aura/View.
     *
     * @return TemplateRegistry
     */
    public function getRegistry()
    {
        return $this->view->getViewRegistry();
    }

    /**
     * @param string $name
     * @param mixed  $service
     */
    public function register($name, $service)
    {
        // TODO: Implement register() method.
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @param string $file
     * @param array  $data
     * @return string
     * @throws \Exception
     */
    public function render($file, $data = [])
    {
        $file = $this->locator->locate($file.'.php');
        if( !$file ) {
            return '';
        }
        $this->view->setData($data);
        $this->view->setView($file);
        return $this->view->__invoke();
    }
}
