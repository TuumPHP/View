<?php
namespace Tuum\View\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Tuum\View\ViewEngineInterface;

class Renderer implements ViewEngineInterface
{
    /**
     * @var Engine
     */
    protected $templates;

    /**
     * @param Engine $templates
     */
    public function __construct($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @param string $view
     * @return Renderer
     */
    public static function forge($view)
    {
        $templates = new Engine($view);
        return new self($templates);
    }

    /**
     * @return Engine
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param ExtensionInterface $extension
     * @return $this
     */
    public function extend($extension)
    {
        $this->templates->loadExtension($extension);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $service
     */
    public function register($name, $service)
    {
        $this->templates->registerFunction($name, $service);
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
        return $this->templates->render($file, $data);
    }
}