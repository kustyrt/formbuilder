<?php

namespace Nifus\FormBuilder;

class FormBuilder
{



    protected
        $config=[],$errors = [],
        $model = false,
        $modelKey = false,
        $nameForm = false,
        $response = false;

    /**
     *
     * @param $nameForm
     */
    public function __construct($nameForm)
    {
        $this->nameForm = $nameForm;
        $this->response = new Response();
    }

    /**
     * @param $nameForm
     * @param array $config
     * @return Form
     */
    static function create($nameForm, $config = array())
    {
        $builder = new self($nameForm);
        $builder->setConfig($config);
        return new Form($builder);
    }
    /**
     * @param $type
     * @param array $config
     * @param string $name
     * @return Fields|InputText
     * @throws ConfigException
     */
    static function createField($type='text', array $config = [], $name = '')
    {
        $class = 'Nifus\FormBuilder\\Input' . ucfirst($type);
        if (!class_exists($class)) {
            throw new ConfigException('Не найден класс ' . $class);
        }
        $class = new $class($name, $config);
        return $class->setType($type);
    }

    /**
     * @param array $config
     * @return Form
     */
    public function setConfig(array $config = [])
    {

        if (!is_array($config)) {
            //throw new ConfigException('Неправильный конфиг');
        }

        //  настройки формы
        $config = $this->setDefaultConfig($config);

        //  настройки полей
        $this->config = $this->setDefaultFieldsConfig($config);
       // return new Form($this->config, $this);
    }

    /**
     * Обрабатываем массив настроек формы
     *
     * @param $config
     */
    protected function setDefaultConfig($config)
    {
        //  одна или много ошибок
        $config['singleError'] = isset($config['singleError']) ? $config['singleError'] : true;

        //  установим параметры относящиеся к форме
        new Form($this);


        //  формат вывода формы
        $config['render'] = (isset($config['render']) && is_array($config['render'])) ? $config['render'] : [];
        $config['render']['format'] = isset($config['render']['format']) ? $config['render']['format'] : 'array';

        return $config;
    }

    /**
     * Устанавливаем значения по-умолчанию для полей формы
     * @param $config
     * @return mixed
     * @throws ConfigException
     */
    protected function setDefaultFieldsConfig($config)
    {
        if (!is_array($config)) {
            throw new ConfigException('Пустой массив fields');
        }
        if (!isset($config['fields'])) {
            return $config;
        }
        foreach ($config['fields'] as $name => $config) {
            // тип элемента
            $type = isset($config['type']) ? 'text' : $config['type'];
            $class = 'Nifus\FormBuilder\\Input' . camelCase($type);
            if (!class_exists($class)) {
                throw new ConfigException('Не найден класс ' . $class);
            }
            $field = new $class($config, $this);
            $field->setDefaultConfig();
        }
        return $config;
    }



    public function setId($id)
    {
        $this->modelKey = $id;
    }

    /**
     * Выдаём результат
     *
     * @return array|string
     * @throws ConfigException
     */
    public function render()
    {
        $render = new Render($this->config, $this);
        return $render->render();
    }

    public function errors()
    {
        if ($this->config['singleError']) {
            return array_shift($this->errors);
        }
        return $this->errors;
    }

    /**
     *  обработка формы
     */
    function save()
    {
        $response = new Response($this->config);
        return $response->save();
    }

    /**
     * Меняем конфиг формы
     * @param $key
     * @param $value
     */
    public function setFormConfig($key, $value)
    {
        $this->config[$key] = $value;
    }

    /**
     * Меняем конфиг поля формы
     * @param $field
     * @param $config
     */
    function setFieldConfig($field, $config)
    {
        $this->config['fields'][$field] = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }
    public function getResponse()
    {
        return $this->response;
    }
}