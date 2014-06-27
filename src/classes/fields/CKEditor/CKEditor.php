<?php

namespace Nifus\FormBuilder\Fields;

class CKEditor extends \Nifus\FormBuilder\Fields\Textarea{



    public function setToolbar($tools){
        if ( is_array($tools) ){
            $this->config['toolbar'] = json_encode($tools);
        }else{
            $this->config['toolbar'] = ($tools);
        }
        return $this;
    }

    public function renderElement($response){
        \Nifus\FormBuilder\Render::jsAdd('ckeditor','CKEditor');
        $v = \View::make('formbuilder::classes/fields/CKEditor/js')
            ->with('config',$this->config )
            ->with('id',$this->config['id']);
        \Nifus\FormBuilder\Render::setJs($v->render(), $v->getPath());

        return parent::renderElement($response);
    }

}