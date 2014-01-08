<?php

namespace Nifus\FormBuilder;

class AjaxExtension{

    static function autoload($formBuilder){
        FormBuilder::jsAdd('jquery');
        FormBuilder::jsAdd('jquery.form','ajax');
        $config = $formBuilder->config;
        if ( isset($config['ajax']) && is_array($config['ajax']) ){
            //dd( \View::make('formbuilder::extends/ajax/js')->render() );
            $v = \View::make('formbuilder::extends/ajax/js')
            ->with('formName',$config['id'])
            ->with('formAction',$config['action'])
            ;

            $formBuilder->setJs(  $v->render(),$v->getPath() );
        }
    }

    static function fields(){
        return [];
    }
}