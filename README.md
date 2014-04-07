formbuilder
===========

 Laravel 4 package for generate HTML form
 
Install 
===========

Formbuilder installs just like any other package, via Composer : `composer require nifus/formbuilder 0.1.x`. 


<<<<<<< HEAD
Then if you're using Laravel, add Formbuilder's Service Provider to you `config/app.php` file :
=======
Then if you're using Laravel, add Flatten's Service Provider to you `config/app.php` file :
>>>>>>> 13c52d564d0d9ae709b931376a88ec0b5b6a0579
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
<<<<<<< HEAD

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

```php
	..
	$form->setExtensions(['Placeholder','Ajax'])
	..
```

```php
	..
	$form->setRegisterExtension('name',function(){
		return new \Nifus\FormBuilder\Extensions\AjaxFileLoader; 
	})
	..
```
=======

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
>>>>>>> 13c52d564d0d9ae709b931376a88ec0b5b6a0579
