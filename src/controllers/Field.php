<?php
namespace Nifus\FormBuilder;

class Field extends \Controller {

    public function Js($ext){

    }
	public function Index($ext,$action)
	{
        $class = 'Nifus\FormBuilder\\'.$ext.'Extension';

        if ( !class_exists($class) ){
            die();
        }

        $c = new $class;
        return $c->$action();
    }





}