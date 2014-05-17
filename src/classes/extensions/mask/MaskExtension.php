<?php
namespace Nifus\FormBuilder\Extensions;

use \Nifus\FormBuilder\Extension as Extension;
class Mask extends Extension
{

    public function loadAsset(){
        FormBuilder::jsAdd('jquery');
        FormBuilder::jsAdd('jquery.mask.min','mask');

        $v = \View::make('formbuilder::classes/extensions/mask/js')
            ->with('form',$this->builder->form_name);
        FormBuilder::setJs(  $v->render(),$v->getPath() );

    }


    public function configField($config){

        if ( isset($config['mask'])   ){
            return ['data-mask'=>$config['mask']];
        }

    }
}