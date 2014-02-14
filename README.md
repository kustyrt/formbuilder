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
	method - post|get
	action - null|URL адрес обработчка формы. Если null то форма будет отправлена по текущему 						адресу
	ajax 	- null|Array[
				url - null|адрес обработчика аякс запроса
				]
	render 	- null | Array[
				format - array|table|ul
				]  - формат вывода формы.
	Extensions  - null | Array 
```

