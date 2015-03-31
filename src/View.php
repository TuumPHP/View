<?php
namespace Tuum\View;

class View implements ViewEngineInterface
{
    /**
     * @var Renderer
     */
    private $renderer;

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
        return $this->renderer->render($file, $data);
    }
}
