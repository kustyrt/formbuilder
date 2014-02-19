<?php

namespace Nifus\FormBuilder;

class Form{


    private
        $builder = false;


    public function __construct($builder){
        $this->builder = $builder;
        $config = $builder->getConfig();
        $def = $this->getDefaultConfig();
        $config = array_merge($def,$config);
        foreach( $config as $key=>$value ){
            $this->setConfig($key,$value);
        }

    }

    public function getConfig(){
        return $this->builder->getConfig();
    }
    public function render(){
        return $this->builder->render();
    }

    protected function getDefaultConfig(){
        return [
            'method' => 'post',
            'enctype'=>'multipart/form-data'
        ];
    }


    /**
     * @param  $format
     * @param array $config
     * @return $this
     */
    public function setRender($format,$config=[]){
        $config = array_merge($config,['format'=>$format]);
        $this->setConfig('render',$config);
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setJquery($flag){
        if ( true===$flag){
            Render::$assetJs['jquery']=null;
        }else{
            Render::$assetJs['jquery']=false;
        }
        return $this;
    }
    public  function setMethod($method){
        if ( empty($method) ){
            $method = 'post';
        }
        $this->setConfig('method',$method);
        return $this;
    }
    public function setAction($action){
        $this->setConfig('method',$action);
        return $this;
    }
    public function setEnctype($enctype){
        if ( empty($enctype) ){
            $enctype = 'multipart/form-data';
        }
        $this->setConfig('enctype',$enctype);
        return $this;
    }
    public function setId($id){
        if ( empty($id) ){
            $id = $this->builder->nameForm;
        }
        $this->setConfig('id',$id);
        return $this;
    }

    /**
     * Подключаем расширения
     *
     * @param array $extensions
     * @return $this
     */
    public function setExtensions(array $extensions){
        $this->setConfig('extensions',$extensions);
        return $this;
    }




    public function setFields(array $fields){
        $fieldsConfig=[];
        foreach( $fields as $field ){
            $config = $field->getConfig();
            list($name,$config)= each($config);
            if ( isset($fieldsConfig[$name]) ){
                throw new ConfigException(' name:' . $name.' уже было определено ранее');
            }
                //  расширение
            $fullConfig = $this->builder->getConfig();
            foreach( $fullConfig['extensions'] as $ext ){
                $class = 'Nifus\FormBuilder\Extensions\\'.$ext.'';
                if ( !class_exists($class) ){
                    throw new ConfigException('Не найден класс '.$class);
                }
                $ext = new $class($fullConfig,$this->builder,$this);
                $f_config = $ext->configField($config);
                if ( !is_array($f_config) ){
                    throw new ConfigException('Расширение '.$class.' должно возвращать массив');
                }
                $config = array_merge($config,$f_config  );

            }
            $this->builder->setFieldConfig($name,$config);
        }
        return $this;
    }


    /**
     * Меняем конфиг
     * @param $key
     * @param $value
     * @return $this
     */
    public function setConfig($key, $value){
        $this->builder->setFormConfig($key,$value);
        return $this;
    }
    private function setFieldConfig($key, $value){
        $this->builder->setFieldConfig($key,$value);
        return $this;
    }
}