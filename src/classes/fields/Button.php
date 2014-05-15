<?php
namespace Nifus\FormBuilder\Fields;

/**
 * Generate Button
 *
 * Class Radio
 * @package Nifus\FormBuilder\Fields
 */
class Button extends \Nifus\FormBuilder\Fields{

    protected $config=['name'=>'se'];


    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    public function renderElement($response){
        $attrs = $this->renderAttrs();

        return '<button '.$attrs.'>'.$this->config['value'].'</button>';
    }





}