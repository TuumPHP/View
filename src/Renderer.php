<?php
namespace Tuum\View;

/**
 * Class Renderer
 *
 * @package Tuum\View
 *          
 * @property Section $section
 */
class Renderer
{
    /**
     * do not render section if the section has this value.
     */
    const NO_SECTION_RENDER = false;

    /**
     * @var LocatorInterface
     */
    public $locator;

    /**
     * @var Section
     */
    private $section;

    /**
     * @var array
     */
    public $services = [];

    /**
     * @var string
     */
    private $view_file = null;

    /**
     * @var string
     */
    private $view_extension = 'php';

    /**
     * @var array
     */
    private $view_data = [];

    /**
     * @var string
     */
    private $layout_file = null;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param LocatorInterface $locator
     * @param null|Section     $section
     */
    public function __construct($locator, $section=null)
    {
        $this->locator = $locator;
        $this->section = $section ?: new Section();
    }

    /**
     * @param string $view_dir
     * @return Renderer
     */
    public static function forge($view_dir)
    {
        return new self(new Locator($view_dir), new Section());
    }

    /**
     * @param string $file
     * @param array  $data
     * @return $this
     */
    public function setLayout($file, array $data = [])
    {
        $this->layout_file = $file;
        $this->view_data   = array_merge($this->view_data, $data);
        return $this;
    }

    /**
     * @param $dir
     * @return $this
     */
    public function setRoot($dir)
    {
        $this->locator->addRoot($dir);
        return $this;
    }

    /**
     * @param string $ext
     * @return $this
     */
    public function setFileExtension($ext)
    {
        $this->view_extension = $ext;
        return $this;
    }

    /**
     * @param string $file
     * @return bool|string
     */
    public function getPath($file)
    {
        return $this->locator->locate($file . '.' . $this->view_extension);
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
            throw new \RuntimeException('no such service in Renderer: ' . $name);
        }
        return $this->services[$name];
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if ($name === 'section') return $this->section;
        return $this->service($name);
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
    public function block($file, $data = [])
    {
        $block              = clone($this);
        $block->layout_file = null;
        return $block->doRender($file, $data);
    }

    /**
     * @param string $file
     * @param string $section
     * @param array  $data
     */
    public function blockAsSection($file, $section, $data = [])
    {
        $this->section->set($section, $this->block($file, $data));
    }

    /**
     * a simple renderer for a raw PHP file.
     * non-polluting execution when rendering a view file.
     *
     * @param string|array $file
     * @param array           $data
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
     * @param string|array $file
     * @param array           $data
     * @return string
     * @throws \Exception
     */
    private function doRender($file, $data)
    {
        $this->view_data               = array_merge($this->view_data, $data);
        if (is_array($file)) {
            foreach($file as $key => $val) {
                $this->section->set($key, $val);
            }
        }
        elseif(!$this->view_file = $this->getPath($file)) {
            return null;
        }
        else {
            $this->setContent($this->renderViewFile());
        }
        if (!isset($this->layout_file)) {
            return $this->section->get('content');
        }
        $layout              = clone($this);
        $layout->layout_file = null;
        return $layout->doRender($this->layout_file, $this->view_data);
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @return string
     * @throws \Exception
     */
    private function renderViewFile()
    {
        try {

            ob_start();
            extract($this->view_data);

            /** @noinspection PhpIncludeInspection */
            include($this->view_file);

            return trim(ob_get_clean(), "\n");

        } catch (\Exception $e) {

            ob_end_clean();
            throw $e;
        }
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->section->get('content');
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->section->set('content', $content);
    }
    // +----------------------------------------------------------------------+
}