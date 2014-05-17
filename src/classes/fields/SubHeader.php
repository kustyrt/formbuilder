<?php
namespace Nifus\FormBuilder\Fields;

/**
 * Generate SubHeader
 *
 * Class SubHeader
 * @package Nifus\FormBuilder\Fields
 */
class SubHeader extends \Nifus\FormBuilder\Fields{
    public
        $breakLine = true;

    protected $config=['label'=>null];

    public function setValue($value)
    {
        $this->label = $value;
        $this->name = md5(time()+rand(0,1000));
        return $this;
    }

    public function renderLabel(){

        return null;
    }

    public function renderElement($response){

        return '<h4>'.$this->config['label'].'</h4>';
    }





}