<?php

namespace Nifus\FormBuilder;

class PlaceholderExtension{

    /**
     * Подключаем в начале
     *
     * @param $formBuilder
     */
    static function autoload($formBuilder){

    }


    static function fields($config){

        if ( !isset($config['placeholder']) && isset($config['label'])  ){
            return ['placeholder'=>$config['label']];
        }else{
            return ['placeholder'=>$config['placeholder']];
        }

    }
}