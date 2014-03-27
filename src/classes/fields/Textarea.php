<?php
namespace Nifus\FormBuilder\Fields;

/**
 * Generate CheckBox
 *
 * Class Radio
 * @package Nifus\FormBuilder\Fields
 */
class Textarea extends \Nifus\FormBuilder\Fields{



    public function renderElement($response){
        $attrs = $this->renderAttrs();
        $data = $response->getData($this->name);

        $elements='<textarea  '.$attrs.' >'.$data.'</textarea>';

        return $elements;
    }





}