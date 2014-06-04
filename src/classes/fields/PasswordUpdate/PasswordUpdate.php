<?php

namespace Nifus\FormBuilder\Fields;

class PasswordUpdate extends \Nifus\FormBuilder\Fields{

    protected
        $config=[
            'data-password'=>'true',
        ];

    public function __construct($typeField,$name='',array $config,$builder){
        $v = \View::make('formbuilder::classes/fields/PasswordUpdate/js');
        \Nifus\FormBuilder\Render::setJs($v->render(), $v->getPath());
    }



}