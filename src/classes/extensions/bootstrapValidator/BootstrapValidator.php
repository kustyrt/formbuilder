<?php
namespace Nifus\FormBuilder\Extensions;

use \Nifus\FormBuilder\Extension as Extension;

class BootstrapValidator extends Extension
{



    public function loadAsset()
    {
        \Nifus\FormBuilder\Render::jsAdd('jquery');

        // валидация
        \Nifus\FormBuilder\Render::jsAdd('validator', 'bootstrap-validator/dist');


        $v = \View::make('formbuilder::classes/extensions/bootstrapValidator/js')
            ->with('formName', $this->builder->form_name);
        \Nifus\FormBuilder\Render::setJs($v->render(), $v->getPath());


    }

    public function configField($config)
    {
        $result = '';
        if (!isset($config['data-required'])) {
            return [];
        }
        $result .= 'required,';
        $types = explode('|', $config['data-required']);

        foreach ($types as $t) {
            if ( preg_match('#^(min|max):([0-9]*)$#iUs',$t,$search) ){
                $result .= $search[1].'Length[' . $search[2] . '],';
            }elseif(preg_match('#^email$#iUs',$t,$search) ){
                $result .= 'email,';
            }
        }
        return ['data-validetta' => $result];

    }
}