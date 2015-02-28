<?php
namespace Tuum\View\Tuum;

use Tuum\Locator\LocatorInterface;
use Tuum\View\ViewEngineInterface;

class Renderer implements ViewEngineInterface
{
    /**
     * @var LocatorInterface
     */
    public $locator;

    /**
     * @var array
     */
    public $services = [];

    /**
     * @var string
     */
    private $view_file = null;

    /**
     * @var array
     */
    private $view_data = [];

    /**
     * @var array
     */
    private $section_data = [];
    
    /**
     * @var Renderer
     */
    private $next = null;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param LocatorInterface $locator
     */
    public function __construct($locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param string $file
     * @param array  $data
     * @return Renderer
     */
    public function withView($file, $data=[])
    {
        $next = clone($this);
        $next->view_file = $file;
        $next->view_data = $data;
        $this->next = $next;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $service
     */
    public function register($name, $service)
    {
        $this->services[$name] = $service;
    }

    /**
     * @param string $name
     * @param array  $args
     * @return mixed|null
     */
    public function __call($name, $args=[])
    {
        return array_key_exists($name, $this->services) ? $this->services[$name] : null;
    }

    // +----------------------------------------------------------------------+
    //  section etc.
    // +----------------------------------------------------------------------+
    /**
     * start capturing a section. 
     */
    protected function startSection()
    {
        ob_start();
    }

    /**
     * end capture with name.
     * 
     * @param string $name
     */
    protected function endSection($name)
    {
        $this->section_data[$name] = ob_get_clean();
    }

    /**
     * get a captured section. 
     * 
     * @param string $name
     * @return string
     */
    protected function getSection($name)
    {
        return array_key_exists($name, $this->section_data) ? $this->section_data[$name]: ''; 
    }

    /**
     * @param array $data
     */
    protected function setSectionData(array $data)
    {
        $this->section_data = $data;
    }

    /**
     * @return string
     */
    protected function getContent()
    {
        return $this->getSection('content');
    }
    // +----------------------------------------------------------------------+
    //  rendering a view file. 
    // +----------------------------------------------------------------------+
    /**
     * a simple renderer for a raw PHP file.
     * non-polluting execution when rendering a view file.  
     *
     * @param string $file
     * @param array  $data
     * @return string
     * @throws \Exception
     */
    public function render($file, $data = [])
    {
        $viewer = clone($this);
        $viewer->view_file = $file;
        return $viewer->doRender($data);
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @param array  $data
     * @return string
     * @throws \Exception
     */
    private function doRender($data)
    {
        $content = $this->renderViewFile($data);
        if (!isset($this->next)) {
            return $content;
        }
        $this->section_data['content'] = $content;
        $next = clone($this->next);
        $next->setSectionData($this->section_data);
        return $next->renderViewFile($this->view_data);
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @param array $__data
     * @return string
     * @throws \Exception
     */
    private function renderViewFile($__data)
    {
        $this->view_data = array_merge($this->view_data, $__data);
        $__file = $this->locator->locate($this->view_file.'.php');
        if( !$__file ) return '';
        try {

            ob_start();
            extract($this->view_data);

            /** @noinspection PhpIncludeInspection */
            include($__file);

            return ob_get_clean();

        } catch (\Exception $e) {

            ob_end_clean();
            throw $e;
        }
    }
    // +----------------------------------------------------------------------+
}