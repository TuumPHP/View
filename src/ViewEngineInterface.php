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
     * a simple renderer for a raw PHP file.
     *
     * @param string|callable $file
     * @param array  $data
     * @return string
     * @throws \Exception
     */
    public function render($file, $data = []);

    /**
     * set layout file.
     *
     * @param string $file
     * @param array  $data
     * @return $this
     */
    public function setLayout($file, $data = []);

    /**
     * set root directory of template files.
     *
     * @param $dir
     * @return $this
     */
    public function setRoot($dir);
}