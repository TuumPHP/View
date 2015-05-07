<?php
namespace Tuum\View;

class Section
{
    /**
     * do not render section if the section has this value.
     */
    const NO_SECTION_RENDER = false;

    /**
     * @var array
     */
    private $section_data = [];

    /**
     * @var string[]
     */
    private $ob_list = [];

    /**
     * start capturing a section.
     *
     * @param string $name
     */
    public function start($name)
    {
        $this->ob_list[] = $name;
        ob_start();
    }

    /**
     * @return string
     */
    private function obPop()
    {
        if (empty($this->ob_list)) {
            return null;
        }
        return array_pop($this->ob_list);
    }

    /**
     * @return bool
     */
    private function obEmpty()
    {
        return empty($this->ob_list);
    }

    /**
     * end capture with name.
     */
    public function save()
    {
        $name = $this->obPop();
        if (!$name) {
            return;
        }
        $this->section_data[$name] = ob_get_clean();
        if (!$this->obEmpty()) {
            echo $this->section_data[$name];
        }
    }

    /**
     * get a captured section.
     *
     * @param string $name
     * @return string
     */
    public function get($name)
    {
        return array_key_exists($name, $this->section_data) ? $this->section_data[$name] : '';
    }

    /**
     * @param string       $name
     * @param string|mixed $data
     * @return $this
     */
    public function set($name, $data)
    {
        $this->section_data[$name] = $data;
        return $this;
    }

    /**
     * @param string $name
     */
    public function markNotToRender($name)
    {
        $this->section_data[$name] = self::NO_SECTION_RENDER;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists($name)
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
     */
    public function renderAs()
    {
        if (!$name = $this->obPop()) {
            return ;
        }
        if ($this->get($name) !== self::NO_SECTION_RENDER) {
            echo ob_get_clean();
        } else {
            ob_end_clean();
        }
    }

    /**
     * render the part of a template only if section $name does not exist.
     */
    public function replaceBy()
    {
        if (!$name = $this->obPop()) {
            return ;
        }
        $content = $this->get($name);
        if ($content === self::NO_SECTION_RENDER) {
            ob_get_clean();
            return; // do not render anything.
        } elseif ($content) {
            ob_get_clean();
            echo $content;
        } else {
            echo ob_get_clean();
        }
    }
}