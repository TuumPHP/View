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

In a layout file, use the defined section:

```php
Block:
<?= $this->getSection('block'); ?>

Content:
<?= $this->getContent(); ?>

Done:
```



Helper
------

to be implemented. 
