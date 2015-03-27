<?php /** @var \Tuum\View\Tuum\Renderer $this */ ?>
<?php $this->blockAsSection('block-menu', 'menu'); ?>

This is block content.
<?= $this->block('block-sub'); ?>
