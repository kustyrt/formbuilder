<?php

namespace Nifus\FormBuilder;

class Extension{

    protected
        $render,$builder = false,
        $config = [];

    public function __construct($config,$builder,$render){
        $this->config = $config;
        $this->builder = $builder;
        $this->render = $render;
    }
}