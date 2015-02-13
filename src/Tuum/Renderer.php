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
     * @var Renderer
     */
    private $next = null;
    
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
        $this->view_file = $file;
        $content = $this->doRender($data);
        if (!isset($this->next)) {
            return $content;
        }
        $this->view_data['_content_'] = $content;
        return $this->next->doRender($this->view_data);
    }
    
    /**
     * a simple renderer for a raw PHP file.
     *
     * @param array $__data
     * @return string
     * @throws \Exception
     */
    private function doRender($__data=[])
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
}