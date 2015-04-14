View Template
===========

Started as a simple raw PHP based template class, this project has __matured__ to be a full featured template component with ability set layouts, sections, and blocks. 

*   Status: alfa. works as is, but subject to change its API. 
*   Immutable: Mostly.
*   PSR: Psr-1, Psr-2, and Psr-4.

This package focuses on rendering only; the escaping strings are done by helpers.  

### Licence

MIT Licence

### Installation

```sh
composer require "tuum/view: 0.2.*"
```

Requires Tuum\Locator component. 


Usage
-----

Constructing the renderer: 

```php
use Tuum\Locator\Locator;
use Tuum\View\Tuum\Renderer;

$viewer = new Renderer(new Locator('/path/to/view/'));
$viewer->render('file-name', [ 
  'some' => 'data' 
]);
```

The template file at ```/path/to/view/file-name.php``` may be: 

```php
<html>
Some=<?= $some; ?>
</html>
```

### Using Layout

You can set a default layout in the renderer: 

```php
$viewer = $viewer->setLayout('layout/layout1');
$viewer->render('file-name', [ 
  'some' => 'data' 
]);
```

or set layout inside individual template file (hence overwrites the default layout): 

```php
<?php
$this->setLayout('layout/layout1');
?>
set layout inside this view.
```

#### layout file

In a layout file, emit content (from the initial template file) using ```$this->getContent()``` method. 

```php
This is layout#1.
<?= $this->getContent(); ?>

End of layout#1.
```


### Using Section

#### in template file

In a template file, define a section using ```$this->startSection()``` 
and ```$this->endSection('block')```,

```php
<?php
$this->startSection();
?>
this is a block.
<?php
$this->endSection('block');
?>
this is a content.
```

#### inl layout file

In a layout file, use the defined section as:

```php
Block:
<?= $this->getSection('block'); ?>

Content:
<?= $this->getContent(); ?>

Done:
```

You can check if a section is defined:

```php
<?php if ($this->sectionExists('content')): ?>
    <?= $this->getContent(); ?>
<?php else: ?>
    Welcome Section Test
<?php endif; ?>
```

Using ```replaceBySection``` in layout file may make it clearer... 

```php
<?php $this->startSection(); ?>
    Welcome Section Test
<?php $this->replaceBySection('content'); ?>
```

#### disabling a section in layout

In the template file, it is possible to set a section not to be displayed at all by using ```markSectionNoRender ``` method. 

In template file: 

```php
<?php $this->markSectionNoRender('bread'); ?>
```

In layout file:

```php
<?php $this->startSection(); ?>
<ol>
    <li>bread-top</li>
    <?= $this->getSection('bread'); ?>
</ol>
<?php $this->renderAsSection('bread'); ?>
```

Because the ```bread``` sectuib us marked as NoRender, the entire bread section will not be rendered. 

This also works for ```replaceBySection``` as well. So, for the example for ```replaceBySection('content')``` will not be displayed at all if marked as NoDisplay. 


### Using Block

To include another template, use ```block``` method in a template file as:

```php
<?= $this->block('block-sub', ['some' =>'value']); ?>
```

The ```block-sub``` is another template file. The template's data is shared with the block template. 

Some times, one might want to use a block as a section (well, I do). So, here's an easy way to do. 

```php
<?php $this->blockAsSection('block-file', 'section-name', ['another' => 'one']); ?>
```


