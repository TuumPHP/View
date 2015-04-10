View Template
===========

Started as a simple raw PHP based template class, this project has __matured__ to be a full featured template component with layouts, sections, and helpers. 

*   Status: alfa. works as is, but subject to change its API. 
*   Immutable: Mostly.
*   PSR: Psr-1, Psr-2, and Psr-4.

Consisted of two parts: ```renderer``` to render a template file, and ```DataView``` helper objects to handle escaping values, etc. 

### Licence

MIT Licence

### Installation

```sh
composer require "tuum/view: 0.2.*"
```

Requires Tuum\Locator component. 


Usage of Renderer
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


DataView Helper
------

### Escape Helper

Use ```Escape``` class to manage escape method when redering a string inside a template. As a default, strings will be escaped using ```htmlspecialchars``` function for HTML files. 

```php
$esc = new Escape();
echo $esc->escape('<danger>safe</danger>');
```

You can specify another escape method at the construction or using ```withEscape``` method:

```php
$esc = new Escape('addslashes');
// or
$esc = $esc->withEscape('rawurlencode');
```

### Data Helper

Use ```Data``` class to display strings and values to template while escaping the values. 


```php
$data = Data::forge(['view'=>'<i>val</i>'], $esc);
echo $data['view'];      // escaped
echo $data->view;        // escaped
echo $data->get('view'); // escaped
echo $data->raw('view'); // raw value
echo $data->get('none', 'non\'s'); // show escaped default value
```

#### iteration

the ```Data``` object implements IteratorAggregate interface. that means,

```php
$data = Data::forge([
    'name1'=>'val1',
    'name2'=>'val2',
]);
foreach($data as $key => $val) {
    echo "$key: $val<br/>"; // don't!
    echo $key, ':', $data[$key],'<br/>';
}
```

Ah, the __iteration will not escape values__. maybe I should remove this functionality... 

So, here's alternative way of using ```getKeys``` method.

```php
foreach($data->getKeys() as $key) {
    echo $key, ':', $data[$key],'<br/>';
}
```

#### hidden tag

a simple method to show a hidden tag:

```php
$data = Data::forge(['_method'=>'put']);
echo $data->hiddenTag('_method');  
// <input type="hidden" name="_method" value="put" />
```

#### extract by key 

use ```extractKey``` method to create a subset of ```Data``` object if the data is  an array of another array (or object).

```php
$data = Data::forge(['obj'=>new ArrayObject['type' => 'object']);
$obj = $data->extractKey('obj');
echo $obj->type;  // object
```

### Inputs Helper

The ```Inputs``` class introduces a convenient way to access array of data using names of HTML form elements. (think Laravel's Input::old values are populated in Form class). 

```php
<?php
$input = Inputs::forge([
    'name' => '<my> name',
    'gender' => 'male',
    'types' => [ 'a', 'c' ],
    'sns' => [
        'twitter' => 'example@twitter.com',
        'facebook' => 'example@facebook.com',
    ],
], $esc);
echo $input->get('name'); // escaped '<my> name'
echo $input->checked('gender', 'male');   // ' checked'
echo $input->checked('gender', 'female'); // empty
vardump($input->get('types')); // ['a', 'c']
echo $input->checked('types', 'a'); // ' checked'
echo $input->checked('types', 'b'); // empty
echo $input->get('sns[twitter]'); // 'example@twitter.com'
```

### Errors Helper

The ```Errors``` maybe used as conjunction with ```Inputs```, where as ```Errors``` for invalidated message for input data. 

```php
<?php
$errors = Errors::forge([
    'name' => 'message for name',
    'gender' => 'gender message',
    'types' => [ 2 => 'message for type:B' ],
    'sns' => [
        'facebook' => 'love messaging facebook?',
    ],
], $esc);
// default format is: <p class="text-danger">%s</p>
echo $errors->get('name'); // message for name
echo $errors->get('gender');   // gender message
echo $errors->get('types[2]'); // message for type:B
echo $errors->get('sns[facebook]'); // love messaging facebook?
```

Notice that the message for type:B has specific index number. In order for generic array input to work with error messages, you have to specify the index. 

To change the format of the error message, just do:

```php
$errors->format = '<div>(*_*) %s</div>';
```

### Message Helper

This helper may not be that generic. Message class is for general message to be displayed in a main contents of a web page.

```php
$message = new Message;
$message->add('hello');
$message->add('whoops', Message::ERROR);
echo $message->onlyOne(); 
// <div class="alert alert-danger">Whoops</div>
```

The ```onlyOne``` method shows only one first message that is most severe. 

### DataView Helper

This helper is to aggregate all the other helpers into one object. Construct as you like and pass it to the renderer; 

```php
$view = new DataView;
$view->data = Data::forge($data);
// more setting

// rendering
$renderer->render('template', ['view' => $view]);
```
