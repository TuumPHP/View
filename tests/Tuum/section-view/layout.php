<?php /** @var \Tuum\View\Tuum\Renderer $this */ ?>
#Section Layout

#Section: breadcrumb
<?php $this->startSection(); ?>
    bread-top
    <?= $this->getSection('bread'); ?>
<?php $this->renderAsSection('bread'); ?>


#Section: menu
<?php $this->startSection(); ?>
    menu-top
    <?= $this->getSection('menu'); ?>
<?php $this->replaceBySection('menu'); ?>


#Section: content
<?php if ($this->sectionExists('content')): ?>
    <?= $this->getContent(); ?>
<?php else: ?>
    Welcome Section Test
<?php endif; ?>
