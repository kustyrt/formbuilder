<?php

namespace Nifus\FormBuilder;

class Fields
{

    protected
        $builder,
        $typeField='text',
        $config = [];


    public function __construct($typeField,$name='',array $config,$builder){
        $this->config = array_merge($this->config,$config);
        $this->name = $name;
        $this->builder=$builder;
        $this->typeField=$typeField;
    }

    public function setValid($rules='',$msg=null){
        $rules = (is_array($rules)) ? implode('|',$rules) :  $rules ;
        $this->set('data-required',$rules);
        if ( isset($msg) ){
            $this->set('data-error-msg',$msg);
        }
        return $this;
    }




    public function setLabel($label)
    {
        $this->label =  $label ;
        return $this;
    }

    public function setValue($value)
    {
        $this->value =  $value ;
        return $this;
    }

    public function setId($id)
    {
        $this->id =  $id ;
        return $this;
    }

    public function setType($type)
    {
        $this->set('type',$type) ;
        return $this;
    }

    public function setName($name)
    {
        $this->name=$name;
        return $this;
    }


    public function setClass($class)
    {
        $this->class=$class;
        return $this;
    }


    public function getConfig(){
        if ( !isset($this->config['name'])  || empty($this->config['name']) ){
            throw new ConfigException('Не указан тип поля для данных ' . var_export($this->config,true));
        }
        //  подключаем правила доп полей.
        if ( !isset($this->config['label']) ){
            $this->config['label'] = isset($this->config['title']) ? $this->config['title']  : trans($this->config['name']);
        }

        $this->config['id'] = isset($this->config['id']) ? $this->config['id'] :   'id_'.$this->clear($this->config['name']);
        return ['type'=>$this->typeField,'name'=>$this->config['name'],'config'=>$this->config];
    }


    public function renderLabel(){
        return '<label for="'.$this->config['id'].'" >'.$this->config['label'].'</label>';
    }
    public function renderElement($response){
        $attrs = $this->renderAttrs();
        $value = $response->getData($this->config['name']);
        if ( !is_null($value) ){
            $attrs.='value="'.htmlspecialchars($value).'"';
        }
        return '<input type="'.$this->typeField.'" '.$attrs.' />';
    }


    public function set($key,$value){
        if ( empty($key) ){
            throw new ConfigException('Пустой ключ');
        }
        $this->config[$key]=$value;
        return $this;
    }

    public  function __set($key,$value){
        return $this->set($key,$value);
    }



    protected function renderAttrs(){
        $attrs = '';
        foreach($this->config as $k=>$v ){
            if ( !is_null($v) && !in_array($k,['data','inline']) ){
                $attrs.=$k.'="'.$v.'" ';
            }
        }
        return $attrs;
    }



    protected function clear($str){
        return strtolower(preg_replace('#[^a-z]#i','',$str));
    }


}