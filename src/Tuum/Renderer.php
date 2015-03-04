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
     * @var string
     */
    private $next_file = null;

    /**
     * @var array
     */
    private $next_data;

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
        $this->next_file = $file;
        $this->next_data = $data;
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
     * @return mixed
     */
    public function service($name)
    {
        if (!array_key_exists($name, $this->services)) {
            throw new \RuntimeException('no such service in Renderer: '. $name);
        }
        return $this->services[$name];
    }
    
    /**
     * @param string $name
     * @param array  $args
     * @return mixed|null
     */
    public function __call($name, $args=[])
    {
        return $this->service($name);
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
    protected function endSectionAs($name)
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
    public function getContent()
    {
        return $this->getSection('content');
    }
    // +----------------------------------------------------------------------+
    //  rendering a view file. 
    // +----------------------------------------------------------------------+
    /**
     * render a block, without default layout.
     *
     * @param string $file
     * @param array  $data
     * @return string
     */
    public function block($file, $data=[])
    {
        $block = clone($this);
        $block->next_file = null;
        return $block->doRender($file, $data);
    }

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
        return $viewer->doRender($file, $data);
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @param string $file
     * @param array  $data
     * @return string
     * @throws \Exception
     */
    private function doRender($file, $data)
    {
        $this->view_file = $file;
        $this->view_data = array_merge($this->view_data, $data);
        $this->section_data['content'] = $this->renderViewFile();
        if (!isset($this->next_file)) {
            return $this->section_data['content'];
        }
        $next_view = clone($this);
        $next_view->next_file = null;
        $next_view->setSectionData($this->section_data);
        return $next_view->doRender($this->next_file, $this->next_data);
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @return string
     * @throws \Exception
     */
    private function renderViewFile()
    {
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