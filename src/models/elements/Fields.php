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
        $this->name = $name;
    }



    protected function getDefaultConfig()
    {
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
    }

    public function setType($type)
    {
        $this->config['type'] =  $type ;

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

        //  подключаем правила доп полей.

        return [$this->name=>$this->config];
    }


}