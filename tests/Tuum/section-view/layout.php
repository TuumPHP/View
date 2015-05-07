<?php /** @var \Tuum\View\Renderer $this */ ?>
#Section Layout

#Section: breadcrumb
<?php $this->section->start(); ?>
    bread-top
    <?= $this->section->get('bread'); ?>
<?php $this->section->renderAs('bread'); ?>


#Section: menu
<?php $this->section->start(); ?>
    menu-top
    <?= $this->section->get('menu'); ?>
<?php $this->section->replaceBy('menu'); ?>


#Section: content
<?php if ($this->section->exists('content')): ?>
    <?= $this->section->get('content'); ?>
<?php else: ?>
    Welcome Section Test
<?php endif; ?>
