<?php

namespace Nifus\FormBuilder;

class CalendarExtension{

    /**
     * Подключаем в начале
     *
     * @param $formBuilder
     */
    static function autoload($formBuilder){

    }


    static function fields($config,$name=null,$formBuilder=null){
        $return=[];
        if ( isset($config['date-format'])   ){
            $return['date-format']=$config['date-format'];
        }
        if ( isset($config['data-value']) ){
            $return['data-value']=$formBuilder->getData($name);
        }
        return $return;

    }
}