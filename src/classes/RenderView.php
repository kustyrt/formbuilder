<?php

namespace Nifus\FormBuilder;


class RenderView{

    private
        $fields,$jsCode,$cssCode;

    function __construct($fields,$js,$css){
        $this->fields = $fields;
        $this->jsCode = $js;
        $this->cssCode = $css;
    }
    public  function field($key){
        if ( isset($this->fields[$key]) ){
            return $this->fields[$key]['element'];
        }
        throw new RenderException('Элемент '.$key.' не найден');
    }
    public  function label($key){
        if ( isset($this->fields[$key]) ){
            return $this->fields[$key]['label'];
        }
        throw new RenderException('Элемент '.$key.' не найден');
    }

    public  function css(){
        return $this->cssCode;
    }
    public  function js(){
        return $this->jsCode;
    }


}