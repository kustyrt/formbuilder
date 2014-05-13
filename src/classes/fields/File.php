<?php

namespace Nifus\FormBuilder\Fields;

class File extends \Nifus\FormBuilder\Fields{

    protected $config=[
        'upload'=>true
    ];

    static function delete(){
        \Log::info('13123');
    }

    public function renderElement($response){
        $path = public_path().$this->config['data-path'];
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$this->config['data-path'];
        $elements = '';
        $attrs = $this->renderAttrs();
        $files = $response->getData($this->config['name']);

        if ( is_array($files) ){
            foreach( $files as $file ){
                $elements.='<br/><a target="_blank" href="'.$url.'/'.$file.'">'.$file.'</a>&nbsp;&nbsp;<!--<a href="'.route('fb.action',['ext'=>'file','action'=>'delete']).'">удалить</a>-->';
            }
        }elseif( !empty($files) ){
            $elements.='<br/><a target="_blank" href="'.$url.'/'.$files.'">'.$files.'</a>&nbsp;&nbsp;';
        }
        $elements.='<br/><input type="file"  '.$attrs.' />';
        return $elements;
    }

    public function setExts()
    {

    }

    public function setSize()
    {

    }

    public function setMultiple()
    {
        $this->config['multiple'] = 'multiple';
        return $this;
    }
    public function setPath($path){
        $this->config['data-path'] = $path;
        return $this;
    }
}