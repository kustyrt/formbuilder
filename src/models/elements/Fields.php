<?php

namespace Nifus\FormBuilder;

class Fields
{
    protected
        $config = [],
        $name;

    public function __construct($config){
        $def = $this->getDefaultConfig();
        $this->config = array_merge($def,$config);

    }

    static function create($name,array $config){
        $field = new self($config);
        $field->name = $name;
        return $field;
    }

    protected function getDefaultConfig()
    {
        return [];
    }

    public function setLabel($label)
    {
        $this->config['label'] =  $label ;
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
    }

    public function getName(){
        return $this->name;
    }
    public function getConfig(){
        return $this->config;
    }


}