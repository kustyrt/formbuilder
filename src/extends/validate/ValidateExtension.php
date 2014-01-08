<?php

namespace Nifus\FormBuilder;

class ValidateExtension{

    /**
     * Подключаем в начале
     *
     * @param $formBuilder
     */
    static function autoload($formBuilder){
        FormBuilder::jsAdd('jquery');
        FormBuilder::jsAdd('jquery.validate.min','validate');
        $lang = \App::getLocale();
        if ( $lang!='en' ){
            FormBuilder::jsAdd('languages/validettaLang-'.\App::getLocale(),'validate');
        }
        FormBuilder::cssAdd('validetta','validate');

        $v = \View::make('formbuilder::extends/validate/js')
        ->with('form',$formBuilder->config['id']);
        $formBuilder->setJs(  $v->render(),$v->getPath() );

    }


    static function fields($config){

        $result='';
        if ( isset($config['required']) ){
            $result.='required,';

            $types=explode(';',$config['required']);

            foreach( $types as $t ){
                $t = explode(':',$t);
                switch($t[0]){
                    case('min'):
                        $result.='minLength['.$t[1].'],';
                        break;
                    case('max'):
                        $result.='maxLength['.$t[1].'],';
                        break;
                    case('type'):
                        $result.=''.$t[1].',';
                        break;
                }
            }
            return ['data-validetta'=>$result];

        }else{
            return [];
        }

    }
}