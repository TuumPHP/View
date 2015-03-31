<?php
namespace Tuum\View;

use Tuum\Locator\Locator;
use Tuum\View\Values\Value;

class View implements ViewEngineInterface
{
    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var Value
     */
    private $value;

    /**
     * @var Locator
     */
    public $locator;

    /**
     * @param Renderer   $renderer
     * @param null|Value $value
     */
    public function __construct($renderer, $value=null)
    {
        $this->renderer = $renderer;
        $this->locator  = $renderer->locator; // bad!
        $this->value = $value;
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
        if ($this->value) {
            $data = ['view' => $this->value->forge($data) ];
        }
        return $this->renderer->render($file, $data);
    }
}
