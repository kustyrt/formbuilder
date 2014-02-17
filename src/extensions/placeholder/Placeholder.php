<?php
namespace Nifus\FormBuilder\Extensions;

use \Nifus\FormBuilder\Extension as Extension;

class Placeholder extends Extension{

    public function loadAsset(){
        //Render::jsAdd('jquery.form','ajax');
    }


    public function configField($config){
        if ( !isset($config['placeholder']) && isset($config['label'])  ){
            return ['placeholder'=>$config['label']];
        }else{
            return ['placeholder'=>$config['placeholder']];
        }
    }

}