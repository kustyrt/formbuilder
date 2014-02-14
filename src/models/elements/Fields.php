<?php

namespace Nifus\FormBuilder;

class Fields extends Form{

   public function setLabel(){
       $fields['label'] = isset($fields['label']) ? $fields['label'] :
           ( isset($fields['title']) ? $fields['title']  : trans($name));
   }

    public function setId(){
        $fields['id'] = isset($fields['id']) ? $fields['id'] : $this->nameForm . '_'.$this->clear($name);

    }

    public function setType(){
        $fields['type'] = isset($fields['type']) ? $fields['type'] : 'text';

    }

    public function setName(){
        $fields['name'] = isset($fields['name']) ? $fields['name'] : $name ;


    }
}