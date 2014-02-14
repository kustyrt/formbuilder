<?php

namespace Nifus\FormBuilder;

class Form{

    protected $config=[];

    function __construct($config,$form){
        $this->config = $config;
        $this->form = $form;
    }


    function formRender(){

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
}