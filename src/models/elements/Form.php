<?php

namespace Nifus\FormBuilder;

class Form{

    protected $config=[];

    function formRender(){

    }

    function __construct($config,$form){
        $def = $this->getDefaultConfig();
        $this->config = array_merge($def,$config);
        $this->form = $form;
        foreach( $this->config as $key=>$value ){
            $this->form->setFormConfig($key,$value);
        }

    }

    protected function getDefaultConfig(){
        return [
            'method' => 'post',
            'enctype'=>'multipart/form-data'
        ];
    }


    public  function setMethod($method){
        if ( empty($method) ){
            $method = 'post';
        }
        $this->form->setFormConfig('method',$method);
        return $this;
    }
    public function setAction($action){
        $this->form->setFormConfig('method',$action);
        return $this;
    }
    public function setEnctype($enctype){
        if ( empty($enctype) ){
            $enctype = 'multipart/form-data';
        }
        $this->form->setFormConfig('enctype',$enctype);
        return $this;
    }
    public function setId($id){
        if ( empty($id) ){
            $id = $this->nameForm;
        }
        $this->form->setFormConfig('id',$id);
        return $this;
    }

    /**
     * Подключаем расширения
     *
     * @param array $extensions
     * @return $this
     */
    public function setExtensions(array $extensions){
        $this->form->setFormConfig('extensions',$extensions);
        return $this;
    }


    public function setFields(array $fields){
        $fieldsConfig=[];
        foreach( $fields as $field ){
            $config = $field->getConfig();
            list($name,$config)= each($config);
            if ( isset($fieldsConfig[$name]) ){
                throw new ConfigException(' name:' . $name.' уже было определено ранее');

            }
            $this->form->setFieldConfig($name,$config);
        }
        return $this;
    }
}