<?php
namespace Nifus\FormBuilder;

class FormBuilder
{
    protected
        //$fields=[],
        $config = [
            'single_error' => true,
            'method' => 'post',
            'enctype'=>'multipart/form-data'
        ],
        $errors = [],
        $model = false,
        $modelKey = false,
        $response = false;


    /**
     * Create new form
     *
     * @param string $idForm
     * @param array $config
     * @return FormBuilder
     */
    static function create($idForm, $config = array())
    {
        $config = is_array($config) ? $config : [];
        $builder = new self($idForm,$config);
        return $builder;
    }

    /**
     * Create new field
     *
     * @param $type
     * @param array $config
     * @param string $name
     * @return Fields|InputText
     * @throws ConfigException
     */
    static function createField($type = 'text', array $config = [], $name = '')
    {
        $class = 'Nifus\FormBuilder\Fields\\' . ucfirst($type);
        if (!class_exists($class)) {
            throw new ConfigException('Не найден класс ' . $class);
        }
        $class = new $class($type,$name, $config,null);
        return $class;
    }



    /**
     * @param  $format
     * @param array $config
     * @return $this
     */
    public function setRender($format,$config=[]){
        $config = array_merge($config,['format'=>$format]);
        return  $this->set('render',$config);
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

    /**
     * @param $method
     * @return $this
     */
    public  function setMethod($method){
        if ( empty($method) ){
            $method = 'post';
        }
        return $this->set('method',$method);
    }

    /**
     * @param $action
     * @return mixed
     */
    public function setAction($action){
        return $this->set('action',$action);
    }


    public function setEnctype($enctype){
        if ( empty($enctype) ){
            $enctype = 'multipart/form-data';
        }
        return $this->set('enctype',$enctype);
    }



    /**
     * Подключаем расширения
     *
     * @param array $extensions
     * @return $this
     */
    public function setExtensions(array $extensions){
        return $this->set('extensions',$extensions);
    }

    /**
     *
     * @param array $fields
     * @return $this
     * @throws ConfigException
     */
    public function setFields(array $fields){
        $fields_config=[];
        foreach( $fields as $field ){
            $config = $field->getConfig();

            $name = $config['name'];
            $type = $config['type'];
            $config = $config['config'];
            if ( isset($fields_config[$name]) ){
                throw new ConfigException(' name:' . $name.' уже было определено ранее');
            }
            //  расширение
            $exts = $this->extensions;
            if ( !is_null($exts) ){
                foreach( $exts as $ext )
                {
                    $class = 'Nifus\FormBuilder\Extensions\\'.$ext.'';
                    if ( !class_exists($class) ){
                        throw new ConfigException('Не найден класс '.$class);
                    }
                    $ext = new $class($this);
                    $f_config = $ext->configField($config);
                    if ( !is_array($f_config) ){
                        throw new ConfigException('Расширение '.$class.' должно возвращать массив');
                    }
                    $config = array_merge($config,$f_config  );

                }
            }
            $fields_config[$name]=['config'=>$config,'type'=>$type];
        }

        $this->fields=$fields_config;
        return $this;
    }


    /**
     * @param $model
     * @return $this
     */
    public  function setModel($model){
        return $this->set('model',$model);
    }

    /**
     * Устанавливаем ключ для загрузки модели
     * @param $id
     */
    public function setId($id)
    {
        $this->modelKey = $id;
        $this->model_key = $id;
    }

    /**
     *  обработка формы
     */
    function save()
    {
        $response = new Response($this->config);
        return $response->save($this->fields);
    }

    public function fails(){
        return $this->response->fails($this->fields);
    }
    public function errors()
    {
        if ($this->config['single_error']) {
            return array_shift($this->errors);
        }
        return $this->errors;
    }

    public function setError($msg){
        $this->errors[]=$msg;
    }

    /**
     * Проверяем, была ли отправлена форма
     * @return bool
     */
    public function isSubmit( ){
        return $this->response->isSubmit();
    }


    /**
     * Выдаём результат
     *
     * @param array $fields список полей для рендинга
     * @return array|string
     * @throws ConfigException
     */
    public function render($fields=array())
    {
        $render = new Render($this,$this->response);
        $render->setFields($this->fields);
        return $render->render($fields);
    }






    public function __construct($nameForm,$config=array())
    {
        $this->form_name=$nameForm;
        $this->response = new Response($this);
        $this->config = array_merge($this->config,$config);
    }



    public function set($key,$value){
        if ( empty($key) ){
            throw new ConfigException('Пустой ключ');
        }
        $this->config[$key]=$value;
        return $this;
    }

    public  function __set($key,$value){
        return $this->set($key,$value);
    }
    public  function __get($key){
        if ( !isset($this->config[$key]) ){
            return null;
        }
        return $this->config[$key];
    }




}