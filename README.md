View Template
=============

A simple raw PHP based template class has __matured__ to 
a full featured template component with layouts, sections, and (helpers). 

Also some experiments on making adapters for Aura and PHP League's template view components. 

status: it works as is but far from complete.

Requires Locator component. 

#### Licence

MIT Licence

#### Psr-4

#### Composer 

only 'dev-master' is available... 

#### Immutable

Uses simple chain to render template and its layout to make it an immutable object, but not tested, yet. 


Usage
-----

```php
use Tuum\Locator\Locator;
use Tuum\View\Tuum\Renderer;

$viewer = new Renderer(new Locator('/path/to/views/'));
$viewer->render('file-name', [ 
  'some' => 'data' 
]);
```

in ```/path/to/view/file-name.php```, 

```php
<html>
Some=<?= $some; ?>
</html>
```

Layout
------

You can set a default layout in the renderer, or set layout 
inside individual template file (hence overwrites the default layout). 

```php
$viewer = $viewer->withView('layout/layout1');
$viewer->render('file-name', [ 
  'some' => 'data' 
]);
```

Or, in a template file, use withView to set layout. 

```php
<?php
$this->withView('layout/layout1');
?>
set layout inside this view.
```

### Layout File

In a layout file, emit content (from the initial template file) using ```$this->getContent()``` method. 

```php
This is layout#1.
<?= $this->getContent(); ?>

End of layout#1.
```


Section
-------

### In Template File

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

### In Layout File

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

### Controlling the Section View

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


Block
-----

To include another template, use ```block``` method in a template file as:

```php
<?= $this->block('block-sub', ['some' =>'value']); ?>
```

The ```block-sub``` is another template file. The template's data is shared with the block template. 

Some times, one might want to use a block as a section (well, I do). So, here's an easy way to do. 

```php
<?php $this->blockAsSection('block-file', 'section-name', ['another' => 'one']); ?>
```


Helper
------

to be implemented. 
