<?php
use Tuum\View\Renderer;

/** @var Renderer $this */

?>
This is layout-with-block.

Block:
<?= $this->section->get('block'); ?>

Content:
<?= $this->getContent(); ?>
