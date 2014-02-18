<?php

namespace Nifus\FormBuilder;

class FormBuilder
{


    protected
        $config = [], $errors = [],
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
        $this->response = new Response($this);
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
    static function createField($type = 'text', array $config = [], $name = '')
    {
        $class = 'Nifus\FormBuilder\Fields\\' . ucfirst($type);
        if (!class_exists($class)) {
            throw new ConfigException('Не найден класс ' . $class);
        }
        $class = new $class($name, $config);
        return $class->setType($type);
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

    public function getConfig($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        } else {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            } else {
                return null;
            }
        }

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
        $this->config = $this->setDefaultConfig($config);
        //  настройки полей
        $this->setDefaultFieldsConfig();
        // return new Form($this->config, $this);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getNameForm()
    {
        return $this->nameForm;
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
     * @return mixed
     * @throws ConfigException
     */
    protected function setDefaultFieldsConfig()
    {
        $fieldsConfig = $this->getConfig('fields');
        $extConfig = $this->getConfig('extensions');
        if ( !isset($fieldsConfig) ) {
            return false;
        }

        foreach ($fieldsConfig as $name => $config) {
            // тип элемента
            $type = !isset($config['type']) ? 'text' : $config['type'];
            $class = 'Nifus\FormBuilder\Fields\\' . ucfirst($type);
            if (!class_exists($class)) {
                throw new ConfigException('Не найден класс ' . $class);
            }
            $field = new $class($name, $config);
            $config = $field->getConfig();
            list($name,$config)= each($config);
            if ( !is_null($extConfig) ){
                foreach ($extConfig as $ext) {
                    $class = 'Nifus\FormBuilder\Extensions\\' . $ext . '';
                    if (!class_exists($class)) {
                        throw new ConfigException('Не найден класс ' . $class);
                    }
                    $ext = new $class($this->config, $this, null);
                    $f_config = $ext->configField($config);
                    if (!is_array($config)) {
                        throw new ConfigException('Расширение ' . $class . ' должно возвращать массив');
                    }
                    $config = array_merge($f_config,$config  );
                }
            }
            $this->setFieldConfig($name,$config);

        }

    }
}