<?php

namespace Nifus\FormBuilder;

class Fields
{
    public $builder;
    protected

        $config = [],
        $name;

    public function __construct($name='',array $config){
        $def = $this->getDefaultConfig();
        $this->config = array_merge($def,$config);
        if ( isset($name) ){
            $this->setName($name);
        }
    }


    public function setBuilder($builder){
        $this->builder = $builder;
    }
    protected function getDefaultConfig()
    {
        if ( !isset($this->config['type']) ){
            return ['type'=>'text'];
        }
        return [];
    }

    public function setConfig($key,$value)
    {
        if ( !isset($key) ){
            throw new ConfigException('Нужно указать ключ');
        }
        $this->config[$key] =  $value ;
        return $this;
    }

    public function setLabel($label)
    {
        $this->config['label'] =  $label ;
        return $this;
    }

    public function setId($id)
    {
        $this->config['id'] =  $id ;
        return $this;
    }

    public function setType($type)
    {
        $this->config['type'] =  $type ;
        return $this;
    }

    public function setName($name)
    {
        $this->config['name'] = $name;
        $this->name = $name;
        return $this;
    }

    public function setClass($class)
    {
        $this->config['class'] = $class;
        return $this;
    }

    public function getName(){
        return $this->name;
    }
    public function getConfig(){
        if ( !isset($this->name)  || empty($this->name) ){
            throw new ConfigException('Не указан тип поля для данных ' . var_export($this->config,true));
        }
        //  подключаем правила доп полей.
        if ( !isset($this->config['label']) ){
            $this->config['label'] = isset($this->config['title']) ? $this->config['title']  : trans($this->name);
        }

        $this->config['id'] = isset($this->config['id']) ? $this->config['id'] :   'id_'.$this->clear($this->name);

        return [$this->name=>$this->config];
    }


    public function renderLabel(){
        return '<label for="'.$this->config['id'].'" >'.$this->config['label'].'</label>';
    }

    protected function renderAttrs(){
        $attrs = '';
        foreach($this->config as $k=>$v ){

            if ( !is_null($v) && !in_array($k,['data']) ){
                $attrs.=$k.'="'.$v.'" ';
            }
        }
        return $attrs;
    }

    public function renderElement($response){
        $attrs = $this->renderAttrs();
        $value = $response->getData($this->name);
        if ( !is_null($value) ){
            $attrs.='value="'.htmlspecialchars($value).'"';
        }
        return '<input '.$attrs.' />';
    }

    protected function clear($str){
        return strtolower(preg_replace('#[^a-z]#i','',$str));
    }


}