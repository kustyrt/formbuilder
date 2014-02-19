<?php

namespace Nifus\FormBuilder;

class Extension{

    protected
      
    $builder;

    protected
        $render,

        $config = [];

    public function __construct($config,$builder,$render){
        $this->config = $config;
        $this->builder = $builder;
        $this->render = $render;
    }
}