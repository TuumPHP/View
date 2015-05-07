<?php /** @var \Tuum\View\Renderer $this */ ?>
#Section Layout

#Section: breadcrumb
<?php $this->section->start('bread'); ?>
    bread-top
    <?= $this->section->get('bread'); ?>
<?php $this->section->renderAs(); ?>


#Section: menu
<?php $this->section->start('menu'); ?>
    menu-top
    <?= $this->section->get('menu'); ?>
<?php $this->section->replaceBy(); ?>


#Section: content
<?php if ($this->section->exists('content')): ?>
    <?= $this->section->get('content'); ?>
<?php else: ?>
    Welcome Section Test
<?php endif; ?>
