<?php

namespace Nifus\FormBuilder;

class FormBuilder{

    public
        $config,$errors = [];
    protected
        $model = false,
        $modelKey = false,
        $nameForm = false;

    /**
     *
     * @param $nameForm
     */
    public function __construct( $nameForm){
        $this->nameForm=$nameForm;
    }


    public function setId($id){
        $this->modelKey = $id;
    }


    /**
     * @param array $config
     * @return bool
     * @throws ConfigException
     */
    public function setConfig(array $config){
        if ( !is_array($config) ){
            throw new ConfigException('Неправильный конфиг');
        }
            //  настройки формы
        $config = $this->setDefaultConfig( $config  );

            //  настройки полей
        $this->config = $this->setDefaultFieldsConfig( $config );
        return true;
    }

    /**
     * Обрабатываем массив настроек формы
     *
     * @param $config
     */
    protected function setDefaultConfig($config){
            //  одна или много ошибок
        $config['singleError'] = isset($config['singleError']) ? $config['singleError'] : true;

        $form = new Form($config,$this);
        $form
            ->setId($config['id'])
            ->setMethod($config['method'])
            ->setEnctype($config['enctype']);

            //  формат вывода формы
        $config['render'] = (is_array($config['render'])) ? $config['render'] : [];
        $config['render']['format'] = isset($config['render']['format']) ? $config['render']['format'] : 'array';

        return $config;
    }

    protected function setDefaultFieldsConfig($config){
        if ( !is_array($config) ){
            throw new ConfigException('Пустой массив fields');
        }
        foreach( $config['fields'] as $name=>$config){
            $fields = $this->config['fields'][$name];



        }
        return $config;
    }



    /**
     * Выдаём результат
     *
     * @return array|string
     * @throws ConfigException
     */
    public function render(){
        $render  = new Render($this->config,$this);
        return $render->render();
    }


    public function errors(){
        if ( $this->config['singleError'] ){
            return array_shift($this->errors);
        }
        return $this->errors;
    }



    /**
     *  обработка формы
     */
    function save(){
        $response  = new Response($this->config);
        return $response->save();
    }

    /**
     * Создаём форму
     * @return Form
     */
    function form(){
        return new Form( $this->config,$this );
    }

    /**
     * Меняем конфиг формы
     * @param $key
     * @param $value
     */
    public function setFormConfig($key,$value){
        $this->config[$key]=$value;
    }

    /**
     * Меняем конфиг поля формы
     * @param $field
     * @param $config
     */
    function setFieldConfig($field,$config){
        $this->config['fields'][$field]=$config;
    }



}