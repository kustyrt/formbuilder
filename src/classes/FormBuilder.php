<?php
namespace Nifus\FormBuilder;

class FormBuilder
{
    public
        $fields=[];
    protected

        $config = [
            'single_error' => true,
            'method' => 'post',
            'enctype'=>'multipart/form-data'
        ],
        $errors = [],
        $model = false,
        $modelKey = false,
        $model_key_value = false,
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
        $render = !is_null($this->render) ? $this->render : [];
        if ( $format=='array' ){
            $format = 'WithoutFormat';
        }else{
            $format = ucfirst($format);
        }
        $config = array_merge($config,['format'=>$format], $render);
        return  $this->set('render',$config);
    }


    public function setCols($cols){
        $config = $this->render;
        if ( is_array($config) ){
            $config = array_merge($config,['cols'=>$cols]);
        }else{
            $config = ['cols'=>$cols];
        }
        return  $this->set('render',$config);
    }


    public function getRender()
    {
        return new Render($this->config, $this);
    }

    /**
     * удаление
     */
    public function setJquery($flag){
        return $this->setLibrary();
    }

    public function setLibrary( $libs=array() ){
        $libs = ( !is_array($libs) || sizeof($libs)==0)  ? ['jquery','bootstrap'] : $libs;
        foreach( $libs as $lib ){
            switch($lib){
                case('bootstrap'):
                    Render::jsAdd('bootstrap');
                    Render::cssAdd('bootstrap');
                    break;
                case('jquery'):
                    Render::jsAdd('jquery');
                    break;
            }
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
        $exts =  $this->extensions;
        if ( is_array($exts) ){
            $exts = array_merge($extensions,$this->extensions );
        }else{
            $exts = $extensions;
        }
        return $this->set('extensions',$exts);
    }

    public function setFields(array $fields,$title=''){
        $fields_config=[];
        foreach( $fields as $field ){
            $config = $field->getConfig();
            $name = $config['name'];
            $type = $config['type'];
            $config = $config['config'];
            if ( empty($name) ){
                $name = $type.rand(1,10000).time();
            }
            if ( !empty($name) && isset($fields_config[$name]) ){
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

        $this->fields[]=['title'=>$title,'fields'=>$fields_config];
        return $this;
    }


    /**
     * Задаём список полей из другой таблицы
     * @param $method
     * @param array $fields
     * @param string $title
     * @return $this
     */
    public function setRelationFields($method,array $fields,$title=''){

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
        //$this->modelKey = $id;
        $this->model_key_value = $id;
        return $this;
    }

    public function getId()
    {
        return $this->model_key_value;
    }

    /**
     *  обработка формы
     */
    function save()
    {
        $response = new Response($this);
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



    public function render($fields=array(),$get_object=false)
    {
        $render_config = $this->render;
        $class = !isset($render_config['format']) ? 'WithoutFormat' :$render_config['format'];
        $class = 'Nifus\FormBuilder\Render\\'.$class.'';
        if ( !class_exists($class) ){
            throw new ConfigException('Не найден класс '.$class);
        }

        $render = new $class($this,$this->response);
        $render->setFields($this->fields);
        if ( false===$get_object ){
        return $render->render($fields);
        }else{
            return $render;
        }
    }

    public function renderAssets()
    {
        $render = new Render($this,$this->response);
        //$render->setFields($this->fields);
        return $render->renderAssets();
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