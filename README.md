View Template
===========

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TuumPHP/View/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/TuumPHP/View/?branch=1.x)

A raw PHP based template with sections, block, and layout, but *no escaping functions*.

To escape values displayed in a template, use other packages, such as [Tuum/Form](https://github.com/TuumPHP/Form).  

### Licence

MIT Licence

### PSR

PSR-1, PSR-2, and PSR-4.

Getting Started
-----

### Installation

```sh
composer require "tuum/view: ^1.0"
```

### Sample Code

Constructing the renderer: 

```php
$view = new Tuum\View\Renderer(
    new Tuum\View\Locator('/path/to/view/')
); // or alternatively,
$view = Tuum\View\Renderer::forge('/path/to/view');
```

To render a PHP template file, 

```php
$view->render('my/file-name', [ 
  'some' => 'data' 
]);
```

The template file at `/path/to/view/my/file-name.php` may be: 

```php
<html>
Some=<?= $some; ?>
</html>
```

**The Tuum/View does not escape the value**. Please use helpers to escape before displaying the values. 

Using Layout
----

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
<?= $this->section-getContent(); ?>

End of layout#1.
```


Using Section
----

### in template file

In a template file, define a section using ```$this->section->start()``` 
and ```$this->section->saveAs('block')```,

```php
<?php $this-> section->start(); ?>

this is a block.

<?php $this->section->saveAs('block'); ?>

this is a content.
```

### in layout file

In a layout file, use the defined section as:

```php
Block:
<?= $this->section->get('block'); ?>

Content:
<?= $this->getContent(); ?>

Done:
```

You can check if a section is defined:

```php
<?php if ($this->section->exists('content')): ?>
    <?= $this->getContent(); ?>
<?php else: ?>
    Welcome Section Test
<?php endif; ?>
```

#### replaceBy method

Using ```replaceBySection``` in layout file may make it clearer... 

```php
<?php $this->section->start(); ?>
    Welcome Section Test
<?php $this->section->replaceBy('content'); ?>
```

```replacedBy``` method will take the output from a section (from ```start()``` till ```replacedBy()``` method) and checks if the named section exists or not. If the section exists, it outputs the existing section, or outputs the section in the layout. 

### disabling a section in layout

In the template file, it is possible to set a section not to be displayed at all by using ```markNotToRender``` method. 

In template file: 

```php
<?php $this->section->markNotToRender('bread'); ?>
```

In layout file:

```php
<?php $this->section->start(); ?>
<ol>
    <li>bread-top</li>
    <?= $this->getSection('bread'); ?>
</ol>
<?php $this->section->renderAs('bread'); ?>
```

Because the ```bread``` section is marked as NotToRender, the entire bread section will not be rendered. 

This also works for ```replaceBySection``` as well. So, for the example for ```replaceBySection('content')``` will not be displayed at all if marked as NoDisplay. 


Using Block
----

To include another template, use ```block``` method in a template file as:

```php
<?= $this->block('block-sub', ['some' =>'value']); ?>
```

The ```block-sub``` is another template file. The template's data is shared with the block template. 

### blockAsSection method

Some times, one might want to use a block as a section (well, I do). So, here's an easy way to do. 

```php
<?php $this->blockAsSection('block-file', 'section-name', ['another' => 'one']); ?>
```


