<?php

namespace Nifus\FormBuilder;

class MaskExtension{

    /**
     * Подключаем в начале
     *
     * @param $formBuilder
     */
    static function autoload($formBuilder){
        FormBuilder::jsAdd('jquery');
        FormBuilder::jsAdd('jquery.mask.min','mask');
        $v = \View::make('formbuilder::extends/mask/js')
            ->with('form',$formBuilder->config['id']);
        $formBuilder->setJs(  $v->render(),$v->getPath() );


    }


    static function fields($config){

        if ( isset($config['mask'])   ){

            return ['data-mask'=>$config['mask']];
        }

    }
}