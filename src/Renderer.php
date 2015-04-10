<?php
namespace Tuum\View;

use Tuum\Locator\LocatorInterface;

class Renderer implements ViewEngineInterface
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
     * @var array
     */
    public $services = [];

    /**
     * @var string
     */
    private $view_name = null;

    /**
     * @var string
     */
    private $view_extension = 'php';

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
    private $layout_file = null;

    /**
     * @var array
     */
    private $layout_data;

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
    public function setLayout($file, $data = [])
    {
        $this->layout_file = $file;
        $this->layout_data = $data;
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
     * @return bool|string
     */
    public function getPath()
    {
        return $this->locator->locate($this->view_name . '.' . $this->view_extension);
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
     * @param array  $args
     * @return mixed|null
     */
    public function __call($name, $args = [])
    {
        return $this->service($name);
    }

    // +----------------------------------------------------------------------+
    //  section etc.
    // +----------------------------------------------------------------------+
    /**
     * start capturing a section.
     */
    public function startSection()
    {
        ob_start();
    }

    /**
     * end capture with name.
     *
     * @param string $name
     */
    public function endSectionAs($name)
    {
        $this->section_data[$name] = ob_get_clean();
    }

    /**
     * get a captured section.
     *
     * @param string $name
     * @return string
     */
    public function getSection($name)
    {
        return array_key_exists($name, $this->section_data) ? $this->section_data[$name] : '';
    }

    /**
     * @param string $name
     */
    public function markSectionNoRender($name)
    {
        $this->section_data[$name] = self::NO_SECTION_RENDER;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function sectionExists($name)
    {
        $names = func_get_args();
        foreach ($names as $name) {
            if (array_key_exists($name, $this->section_data)) {
                return true;
            }
        }
        return false;
    }

    /**
     * render the part of template as $name section.
     * will not render if the section is marked as NO_RENDER
     *
     * @param string $name
     */
    public function renderAsSection($name)
    {
        $content = ob_get_clean();
        if ($this->getSection($name) !== self::NO_SECTION_RENDER) {
            echo $content;
        }
        unset($content);
    }

    /**
     * render the part of a template or render $name section if it exist.
     *
     * @param string $name
     */
    public function replaceBySection($name)
    {
        $content = ob_get_clean();
        if ($this->getSection($name) === self::NO_SECTION_RENDER) {
        } elseif ($this->sectionExists($name)) {
            echo $this->getSection($name);
        } else {
            echo $content;
        }
        unset($content);
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
        $block                        = $this->block($file, $data);
        $this->section_data[$section] = $block;
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
        $this->view_name               = $file;
        $this->view_data               = array_merge($this->view_data, $data);
        $this->section_data['content'] = $this->renderViewFile();
        if (!isset($this->layout_file)) {
            return $this->section_data['content'];
        }
        $layout              = clone($this);
        $layout->layout_file = null;
        $layout->setSectionData($this->section_data);
        return $layout->doRender($this->layout_file, $this->layout_data);
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @return string
     * @throws \Exception
     */
    private function renderViewFile()
    {
        $__file = $this->getPath();
        if (!$__file) {
            return '';
        }
        try {

            ob_start();
            extract($this->view_data);

            /** @noinspection PhpIncludeInspection */
            include($__file);

            return trim(ob_get_clean(), "\n");

        } catch (\Exception $e) {

            ob_end_clean();
            throw $e;
        }
    }
    // +----------------------------------------------------------------------+
}