<?php

namespace Nifus\FormBuilder;

class MultiUploadExtension{

        //  вызывается
    function upload(){
        //dd($_FILES);
        echo '{"files": [
  {
    "name": "picture1.jpg",
    "size": 902604,
    "url": "http:\/\/example.org\/files\/picture1.jpg",
    "thumbnailUrl": "http:\/\/example.org\/files\/thumbnail\/picture1.jpg",
    "deleteUrl": "http:\/\/example.org\/files\/picture1.jpg",
    "deleteType": "DELETE"
  },
  {
    "name": "picture2.jpg",
    "size": 841946,
    "url": "http:\/\/example.org\/files\/picture2.jpg",
    "thumbnailUrl": "http:\/\/example.org\/files\/thumbnail\/picture2.jpg",
    "deleteUrl": "http:\/\/example.org\/files\/picture2.jpg",
    "deleteType": "DELETE"
  }
]}';
    }
    /**
     * Подключаем в начале
     *
     * @param $formBuilder
     */
    static function autoload($formBuilder){
        FormBuilder::jsAdd('jquery');
        FormBuilder::jsAdd('js/jquery.MultiFile','multiUpload');

    }

    /**
     * @param $name
     * @param $config
     * @param $formBuilder
     * @param $attrs  список атрибутов
     * @return array
     */
    static function element($name,$config,$formBuilder,$attrs){

        /*$v = \View::make('formbuilder::extends/upload/js')
            ->with('id','result_'.$config['id'])
            ->with('name',$config['id']);

        $formBuilder->setJs(  $v->render(),$v->getPath() );*/
        //imageUpload
        return [
            'label'=>'<label for="'.$config['id'].'"  name="'.$name.'">'.$config['label'].'</label>',
            'element'=>'<input maxlength="2" id="'.$config['id'].'" name="'.$name.'" '.$attrs.'><div id="result_'.$config['id'].'"></div>'
        ];
    }


    static function fields($config){

        if ( isset($config['type']) &&  $config['type']=='Upload'  ){

            return ['class'=>'multi'];
        }

    }
}