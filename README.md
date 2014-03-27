formbuilder
===========

 Laravel 4 package for generate HTML form
 
Install 
===========

Formbuilder installs just like any other package, via Composer : `composer require nifus/formbuilder 0.1.x`. 


Then if you're using Laravel, add Flatten's Service Provider to you `config/app.php` file :
```php
 	'providers' => array(
        // ...
        'Nifus\Formbuilder\FormbuilderServiceProvider',
    ),
```

And:

```php
 	'aliases' => array(
        // ...
        'Formbuilder' => 'Nifus\Formbuilder\Facade',
    ),
```

Using 
===========


Create form with ID `login`:

```php
	$form = new Formbuilder('login');
```

Set config form
```php
	$form->setAction('/login')
		->setMethod('post')
		..
```

Set Fields
```php
	$form->setFields(
		[
			Formbuilder::createField('text')->setName('login')->setLabel('Login'),
			Formbuilder::createField('password')->setName('pass')->setLabel('Password'),
		]
	)
	..
```

Show result
```php
	$form->render();
```

Extend Fields 
===========

Extendtions  
===========
