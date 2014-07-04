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
        $attrs = $this->renderAttrs();

        if ( !$response->isCreate() ){
            $this->config['data-value-exists']=1;
            $attrs = $this->renderAttrs();
            if (isset($attrs['data-required']) ){
                unset($attrs['data-required']);
            }
        }

        $elements='<input type="password"  '.$attrs.' />';
        return $elements;
    }



}