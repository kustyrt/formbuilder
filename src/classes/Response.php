<?php

namespace Nifus\FormBuilder;

class Response{

    private  $builder,$model=false;

    function __construct($builder){
        $this->builder = $builder;
    }

    /**
     * Проверяем форму
     */
    public function fails($fields){
        $rules = $data_config = $errors_msg=[];
        foreach( $fields as $name=>$config ){
            $config=$config['config'];
            if ( !isset($config['data-required']) ){
                continue;
            }
            if ( isset($config['data-error-msg']) ){
                $errors_msg[$name]=$config['data-error-msg'];
            }

            $rules[$name][]='required';
            $parameters = explode(',',$config['data-required']);
            foreach( $parameters as $parametr ){
                $rules[$name][]=$parametr;
            }
            $data_config[$name] = $this->getResponseData($name);
        }
        $check = \Validator::make(
            $data_config,
            $rules
        );

        if ( false===$check->fails() ){
            return false;
        }
        $errors = $check->failed();
        foreach( $errors as $key=>$info){
            $msg = isset($errors_msg[$key]) ?  $errors_msg[$key] : $key;
            $this->builder->setError($msg);
        }

        return true;
    }

    /**
     *
     */
    function save($fields){
        $data_config = $this->builder->data;
        $id = $this->getResponseData( $this->builder->form_name.'_formbuilderid' );

        if ( !is_null($id) ){
            $model = $this->builder->model;
            $model = $model::find($id);

        }else{
            $model = new $this->builder->model;
        }

        foreach( $fields as $name=>$config ){
            $config=$config['config'];
            if ( isset($config['data']['method']) ){
                $object = new $this->builder->model;
                $f = $object->$config['data']['method']();
                //  пропускаем
                if (  $f instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany  ){
                    continue;
                }
            }

            $model->$name = $this->getResponseData($name);
        }



        $model->save();
        $id = $model-> getKey();

        foreach( $fields as $name=>$config ){
            $config=$config['config'];

            if ( isset($config['data']['method']) ){
                $f = $model->$config['data']['method']();
                //  пропускаем
                if (  !$f instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany  ){
                    continue;
                }

                $key = $f->getForeignKey();
                $prime_key = $f->getOtherKey();
                $rows = $this->getResponseData($name);

                $inc=[];
                if ( is_array($rows) ){
                    foreach($rows as $v) {
                        $inc[$id][]= $v;
                    }
                    foreach( $inc as $id_model=>$keys ){
                        $f = $model::find($id_model);
                        $f->$config['data']['method']()->sync($keys);
                    }
                }
            }
        }
        return true;
    }


    /**
     * Возвращаем данные переданные формой
     */
    public function responseData(){
        $responce = [];
        foreach( $this->config['fields'] as $name=>$config){
            $value = $this->getResponseData( $name ) ;
            if ( !is_null($value) ){
                $responce[$name] = $value;
            }
        }
        return $responce;
    }

    function setError($error){
        $this->errors[]=$error;
    }


    protected function getResponseData($key){
        $key=preg_replace('#\[\]#','',$key);

        $config= $this->builder->method;
        switch($config){
            case('post'):
                return (isset($_POST[$key])) ? $_POST[$key] : null;
                break;
            case('get'):
                return (isset($_GET[$key])) ? $_GET[$key] : null;
                break;
        }
    }

    protected function getModelData($key){
        $model= $this->builder->model;
        if ( is_null($model) ){
            return null;
        }

        if ( false===$this->model ){
            $model = $model::find($this->builder->model_key_value);
            if ( is_null($model) ){
                return null;
            }
            $this->model=$model;
        }

        $configKey = $this->builder->fields[$key];
        $config=$configKey['config'];

        if ( isset($config['data']['method'])   ){
            $rel = $this->model->$config['data']['method']();
            if (  $rel instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany  ){
                return $rel-> getRelatedIds() ;
            }
        }
        return $this->model->$key;

    }




    public function getData($key){
        if ( $this->isSubmit() ){
            return $this->getResponseData($key);
        }else{
            return $this->getModelData($key);
        }

    }
    /**
     * Проверяем отправку формы
     */
    public function isSubmit(){
        return !is_null($this->getResponseData( $this->builder->form_name.'_formbuildersubmit') ) ? true : false;
    }





}