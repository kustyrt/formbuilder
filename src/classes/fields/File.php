<?php

namespace Nifus\FormBuilder\Fields;

class File extends \Nifus\FormBuilder\Fields{


    public function renderElement($response){

        $attrs = $this->renderAttrs();
        $elements='<input type="file"  '.$attrs.' />';
        return $elements;
    }

    public function setExts()
    {

    }

    public function setSize()
    {

    }
}