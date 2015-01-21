<?php
namespace Tuum\View;

/**
 * Interface RendererInterface
 * 
 * an interface for rendering a view file or a template. 
 *
 * @package Tuum\View
 */
interface ViewEngineInterface
{
    /**
     * @param string $name
     * @param mixed  $service
     */
    public function register($name, $service);

    /**
     * a simple renderer for a raw PHP file.
     *
     * @param string $file
     * @param array  $data
     * @return string
     * @throws \Exception
     */
    public function render($file, $data = []);
}