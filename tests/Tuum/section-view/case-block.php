<?php /** @var \Tuum\View\Renderer $this */ ?>
<?php $this->blockAsSection('block-menu', 'menu'); ?>

This is block content.
<?= $this->block('block-sub'); ?>
