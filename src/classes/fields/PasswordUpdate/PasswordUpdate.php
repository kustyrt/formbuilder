<?php

namespace Nifus\FormBuilder\Fields;

class PasswordUpdate extends \Nifus\FormBuilder\Fields{

    protected
        $config=[
            'data-password'=>'true',
        ];

    public function __construct($typeField,$name='',array $config,$builder){
        \Nifus\FormBuilder\Render::jsAdd('password','PasswordUpdate');

        parent::__construct($typeField,$name,$config,$builder);

    }

    public function renderElement($response){
        if ( !$response->isCreate() ){
            $this->config['data-value-exists']=1;
        }
        $attrs = $this->renderAttrs();

        $elements='<input type="password"  '.$attrs.' />';
        return $elements;




    }



}