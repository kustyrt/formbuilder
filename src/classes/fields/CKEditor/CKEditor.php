<?php

namespace Nifus\FormBuilder\Fields;

class CKEditor extends \Nifus\FormBuilder\Fields\Textarea{





    public function renderElement($response){
        \Nifus\FormBuilder\Render::jsAdd('ckeditor','CKEditor');

        //\Log::info();
        $v = \View::make('formbuilder::classes/fields/CKEditor/js')->with('id',$this->config['id']);
        \Nifus\FormBuilder\Render::setJs($v->render(), $v->getPath());

        return parent::renderElement($response);
    }

}