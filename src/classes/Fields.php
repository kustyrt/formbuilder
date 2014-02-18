<?php

namespace Nifus\FormBuilder;

class Fields
{
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



    protected function getDefaultConfig()
    {
        if ( !isset($this->config['type']) ){
            return ['type'=>'text'];
        }
        return [];
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

    public function renderElement($response){
        $attrs = '';
        foreach($this->config as $k=>$v ){
            if ( !in_array($k,['title','id','type','name','style','value' ]) ){
                continue;
            }
            if ( !is_null($v) ){
                $attrs.=$k.'="'.$v.'" ';
            }
        }
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