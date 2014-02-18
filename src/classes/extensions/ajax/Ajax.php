<?php
namespace Nifus\FormBuilder\Extensions;

use \Nifus\FormBuilder\Extension as Extension;
class Ajax extends Extension{

    static function form(){
        //dd($_POST);
        return true;
    }

    public function loadAsset(){

        Render::jsAdd('jquery');
        Render::jsAdd('jquery.form','ajax');

        $config = $this->render->config;
        if ( isset($config['ajax']) && is_array($config['ajax']) ){
            //dd( \View::make('formbuilder::extends/ajax/js')->render() );
            $v = \View::make('formbuilder::extends/ajax/js')
                ->with('formName',$config['id'])
                ->with('formAction',$config['ajax']['url']);
            $this->render->setJs(  $v->render(),$v->getPath() );
        }

    }


    public function configField($config){
        return [];
    }
}