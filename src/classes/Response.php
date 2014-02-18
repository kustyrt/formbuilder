<?php

namespace Nifus\FormBuilder;

class Response{

    private  $builder;

    function __construct($builder){
        $this->builder = $builder;
    }
    /**
     *
     */
    function save(){

        $id = $this->getResponseData( $this->builder->nameForm.'_formbuilderid' );

        if ( !is_null($id) ){
            $model = $this->config['model'];
            $model = $model::find($id);

        }else{
            $model = new $this->config['model'];
        }
        foreach( $this->config['fields'] as $name=>$config ){
            if ( isset($config['data']['method']) ){
                $object = new $this->config['model'];
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

        foreach( $this->config['fields'] as $name=>$config ){
            if ( isset($config['data']['method']) ){
                $f = $model->$config['data']['method']();


                //  пропускаем
                if (  !$f instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany  ){
                    continue;
                }

                //dd(\Lex\Branch::find(52)->SubCurrency()->get());

                $key = $f->getRelated()->getKeyName();

                $prime_key = $model->getKeyName();

                /*$rows = $f->get();
                foreach( $rows as $row )
                {
                    $row->delete();
                }*/
                $rows = $this->getResponseData($name);
                $inc=[];
                if ( is_array($rows) ){
                    foreach($rows as $k=>$v) {
                        $inc[]= [$key=>$v,$prime_key=>$id];
                    }
                }
                $f->sync($inc);


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
        $config= $this->builder->getConfig('method');
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
        $model= $this->builder->getConfig('model');
        if ( is_null($model) ){
            return null;
        }

       /* if ( false===$this->model ){
            $model = $model::find($this->modelKey);
            if ( is_null($model) ){
                return null;
            }
            $this->model=$model;
        }

        $configKey = $this->config['fields'][$key];
        if ( isset($configKey['data']['method'])   ){
            $rel = $this->model->$configKey['data']['method']();
            if (  $rel instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany  ){
                return $rel-> getRelatedIds() ;

            }
        }
        return $this->model->$key;*/

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
        return !is_null($this->getResponseData( $this->builder->getNameForm().'_formbuildersubmit') ) ? true : false;
    }





}