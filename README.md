formbuilder
===========

 Laravel 4 пакет для генерации форм
 
Использование 
===========


Создаё форму, указывая её ID ResetPasswordForm:

```php
	$f = new \Nifus\FormBuilder\FormBuilder('ResetPasswordForm');
```

Передаём массив данных для генерации 
```php
        $f->setConfig( [
            'method' => 'post',
            'action' => null,
            'engine' => null,
            'ajax' => ['url' => route('user.reset_password')],
            'fields'=>[
                'email'=>['title'=>'E-mail', 'required'=>'min:3;'],
            ],
            'render'=>['format' => 'array'],
            'Extensions' => ['Ajax']
        ] );
```

Получаем результат работы
```php
$f->render() 
```
 
Параметры 
===========

В массиве данных можно указывать следующие параметры:
```
	method - null | post | get  
		По умолчанию post

	enctype  - null | application/x-www-form-urlencoded | multipart/form-data | text/plain
		По умолчанию multipart/form-data

	action -  null | URL 
		адрес обработчка формы. 
		Если null то форма будет отправлена по текущему адресу

	render 	- null | Array[
				format - array|table|ul
			]  
		Формат вывода формы. 
		По умолчанию array.


	extensions  - null | Array 
		список расширений подключаемых для генерации формы

	fields - Array
		Список полей формы

	Также есть целый ряд ключей которые используются расширениями для модификации формы
```

