<?php

namespace Nifus\FormBuilder;

class FormBuilder{
    static
    //$formCount = 0,
        $assetJs = [
            'jquery' => false,
            //'require'=>null,
            'engine'=>null
    ],
    $assetCss = [

    ];
    static $staticJs = '';
    public
        $config,$errors = [];
    protected
        $model = false,
        $modelKey = false,
        $nameForm = false;

    /**
     *
     * @param $nameForm
     */
    public function __construct( $nameForm){
        $this->nameForm=$nameForm;
        //self::$formCount++;
    }


    public function setId($id){
        $this->modelKey = $id;
    }


    /**
     * @param array $config
     * @return bool
     * @throws ConfigException
     */
    public function setConfig(array $config){
        if ( !is_array($config) ){
            throw new ConfigException('Неправильный конфиг');
        }
        $this->config=$config;
        $this->config['singleError'] = isset($this->config['singleError']) ? $this->config['singleError'] : true;

        $this->config['method'] = isset($this->config['method']) ? $this->config['method'] : 'post';
        $this->config['action'] = isset($this->config['action']) ? $this->config['action'] : false;

        $this->config['id'] = $this->nameForm;
        $this->config['render']['format'] = isset($this->config['render']['format']) ? $this->config['render']['format'] : 'table';
        foreach( $this->config['fields'] as $name=>$config){
            $this->config['fields'][$name]['label'] =
                isset($config['label'])
                    ? $config['label'] :
                        ( isset($config['title']) ? $config['title']  : trans($name));

            $this->config['fields'][$name]['type'] = isset($config['type']) ? $config['type'] : 'text';
            $this->config['fields'][$name]['id'] = isset($config['id']) ? $config['id'] : $this->nameForm . '_'.$this->clear($name);
            //$this->config['fields'][$name]['name'] = $this->nameForm . '_'.$name;
            $this->config['fields'][$name]['name'] = isset($config['name']) ? $config['name'] : $name ;
        }
        self::$assetJs['jquery'] = (false===(self::$assetJs['jquery'])) && !isset($this->config['jquery']) ? false : null;
            //  расширения
        foreach( $this->config['Extensions'] as $ext ){
            $class = 'Nifus\FormBuilder\\'.$ext.'Extension';
            if ( !class_exists($class) ){
                echo $class;
                continue;
            }
            $class::autoload($this);
        }

        if ( isset($this->config['ajax'])  ){
            self::jsAdd('jquery.form');
        }*/
        return true;
    }


    public function render(){
        switch($this->config['render']['format']){
            case('table'):
                return
                    $this->formRender($this->tableRender()).
                    $this->cssRender().
                    $this->jsRender();
                break;
            case('ul'):
                break;
            case('dev'):
                break;
            case('array'):
                return [
                    'fields' => $this->arrayRender(),
                    'js'=>$this->jsRender(),
                    'css'=>$this->cssRender(),
                    'errors'=>$this->errors(),
                ];
                break;
            default:
                throw new ConfigException(' Неправильный формат вывода '.$this->config['render']['format'] );
                break;
        }

    }

    public function errors(){
        if ( $this->config['singleError'] ){
            return array_shift($this->errors);
        }
        return $this->errors;
    }


    protected function formRender($content){

        $form=$this->setLine('<form enctype="multipart/form-data" method="'.$this->config['method'].'" id="'.$this->nameForm.'">');
        $form.=$content;
        $form .= $this->setLine('</form>');

        return $form;
    }

    protected function arrayRender(){
        $elements = array();
        foreach( $this->config['fields'] as $name=>$config){
            $elements[$name] = $this->elementRender($name,$config);
        }
        return $elements;
    }

    protected function tableRender(){
        $table = $this->setLine('<table class="formBuilder">');
        foreach( $this->config['fields'] as $name=>$config){
            $elementRender = $this->elementRender($name,$config);
            $table.=$this->setLine('<tr class="'.$name.'">');
            $table.=$this->setLine('<td>');
            $table.=$this->setLine($elementRender['label'].'');
            $table.=$this->setLine('</td>');
            $table.=$this->setLine('<td>');
            $table.=$this->setLine($elementRender['element']);
            $table.=$this->setLine('</td>');
            $table.=$this->setLine('</tr>');
        }
        $table.=$this->setLine('<tr>');
        $table.=$this->setLine('<td colspan="2"><input type="submit" /></td>');
        $table.=$this->setLine('</tr>');
        $table .= $this->setLine('</table>');


        return $table;
    }

    /**
     * Собираем элемент из его характеристик
     * @param $name
     * @param $config
     * @return array
     */
    protected function elementRender($name,$config){



        $attrs = '';
        foreach($config as $k=>$v ){
            if ( !in_array($k,['title','id','type','name','style','value','class' ]) ){
                continue;
            }
            if ( !is_null($v) ){
                $attrs.=$k.'="'.$v.'" ';
            }
        }
        //  load
        foreach( $this->config['Extensions'] as $ext ){
            $class = 'Nifus\FormBuilder\\'.$ext.'Extension';
            if ( !class_exists($class) ){
                continue;
            }
            $fields = $class::fields($config,$name,$this);
            if ( sizeof($fields)>0 ){
                foreach($fields as $k=>$v ){
                    if ( !is_null($v) ){
                        $attrs.=$k.'="'.$v.'" ';
                    }
                }
            }

        }




        switch($config['type']){


            case('file'):
                $value = $this->getData($name);
                if ( !is_null($value) ){
                    $attrs.='value="'.htmlspecialchars($value).'"';
                }
                return [
                    'label'=>'<label for="'.$config['id'].'" >'.$config['label'].'</label>',
                    'element'=>'<input type="file"  id="'.$config['id'].'"  '.$attrs.' />',
                ];
                break;
            case('text'):
                $value = $this->getData($name);
                if ( !is_null($value) ){
                    $attrs.='value="'.htmlspecialchars($value).'"';
                }
                return [
                        'label'=>'<label for="'.$config['id'].'" >'.$config['label'].'</label>',
                        'element'=>'<input type="text"  id="'.$config['id'].'"  '.$attrs.' />',
                ];
                break;
            case('password'):
                $value = $this->getData($name);
                if ( !is_null($value) ){
                    $attrs.='value="'.htmlspecialchars($value).'"';
                }
                return [
                        'label'=>'<label for="'.$config['id'].'" >'.$config['label'].'</label>',
                        'element'=>'<input type="password"  id="'.$config['id'].'"  '.$attrs.'  />',
                ];
                break;
            case('hidden'):
                $value = $this->getData($name);
                if ( !is_null($value) ){
                    $attrs.='value="'.htmlspecialchars($value).'"';
                }
                return [
                    'label'=>null,
                    'element'=>'<input type="hidden"  id="'.$config['id'].'"  '.$attrs.'   />',
                ];
                break;
            case('textarea'):
                $value = $this->getData($name);

                return [
                    'label'=>'<label for="'.$config['id'].'"  name="'.$name.'">'.$config['label'].'</label>',
                    'element'=>'<textarea name="'.$config['name'].'" id="'.$config['id'].'" '.$attrs.'>'.htmlspecialchars($value).'</textarea>'
                ];
                break;
            case('select'):

                $data = $this->selectDataFormat($config['data'],$this->getData($name) );
                $multi='';
                if ( $this->isMultiple($config['data']) ){
                    $size= isset($config['data']['size']) ? $config['data']['size'] : 5;
                    $multi = 'multiple="multiple" size="'.$size.'"';
                }
                return [
                    'label'=>'<label for="'.$config['id'].'"  name="'.$name.'">'.$config['label'].'</label>',
                    'element'=>'<select '.$multi.' name="'.$config['name'].'" id="'.$config['id'].'" '.$attrs.'>'.$data.'</select>'
                ];
                break;
            case('radio'):
                $select = $this->getData($name);
                if ( is_null($select) && isset($config['data']['default']) ){
                    $select=$config['data']['default'];
                }
                $elements = [];
                $labels = [];
                foreach( $config['data']['source'] as $key=>$value ){
                    $selected = ($key==$select) ? 'checked="checked"' : '';
                    $elements[$key]='<input type="radio" value="'.$key.'" '.$selected.'  id="'.$config['id'].'"  '.$attrs.' />';
                    $labels[$key]='<label for="'.$config['id'].'" >'.$value.'</label>';
                }
                return [
                    'label'=>$labels,
                    'element'=>$elements,
                ];
                break;
            default:
                if ( in_array($config['type'],$this->config['Extensions'] ) ){
                    $class = 'Nifus\FormBuilder\\'.$config['type'].'Extension';
                    if ( !class_exists($class) ){
                        continue;
                    }
                    return $class::element($name,$config,$this,$attrs);
                }else{
                    die('Неизвестный тип:'.$config['type']);
                }
                //imageUpload
                break;
        }
        return [];
    }

    function isMultiple($config){

        if ( isset($config['multiple']) && true===$config['multiple'] ){
            return true;
        }
        if ( !isset($config['method']) ){
                   return false;
        }
        $object = new $this->config['model'];
        $f = $object->$config['method']();
        if (  $f instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany  ){

            return true;
        }
        return false;
    }
    function selectDataFormat($config,$data=null){
        $select = isset($data) ? $data : ( isset($config['default']) ? $config['default'] : null)  ;
        if ( !is_array($select) ){
            $select=[$select];
        }
        if ( isset($config['selected_mutator']) ){
            $select =$config['selected_mutator']($data);
        }
        $data='';
        $config['type'] = (isset($config['type'])) ? $config['type'] :
            (isset($config['method']) ? 'model' : null );

        if ( $config['type']=='value' ){
            foreach($config['source'] as $key=>$value ){

                $selected = in_array($value,$select) ? 'selected="selected"' : '';
                $data.='<option '.$selected.' value="'.htmlspecialchars($value).'">'.htmlspecialchars($value).'</option>';
            }
        }elseif( $config['type']=='keyvalue'){
            foreach($config['source'] as $key=>$value ){
                if ( is_array($value) ){
                    $data.='<optgroup  label="'.htmlspecialchars($key).'">';
                    foreach($value as $key2=>$value2 ){
                        $selected = in_array($key2,$select) ? 'selected="selected"' : '';
                        $data.='<option '.$selected.' value="'.htmlspecialchars($key2).'">'.htmlspecialchars($value2).'</option>';
                    }
                    $data.='</optgroup>';

                }else{
                    $selected = in_array($key,$select) ? 'selected="selected"' : '';
                    $data.='<option '.$selected.' value="'.htmlspecialchars($key).'">'.htmlspecialchars($value).'</option>';
                }

            }
        }elseif( $config['type']=='model'){
            $values = [];
            if ( isset($config['value']) ){
                preg_match_all('#\{([^}]*)\}#iUs',$config['value'],$find);
                if (sizeof($find[1])>0){
                    $values=$find[1];
                }else{
                    $values=[$config['value']];
                    $config['value']='{'.$config['value'].'}';
                }
            }else{
                $values = ['title'];
            }

            $object = new $this->config['model'];
            $f = $object->$config['method']();
            if (  $f instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo  ){
                //  связь один ко многим

                //  получаем модель связанную
                $related = $f->getRelated();
                $key = $related->getForeignKey();   //  ключ связь для основной таблицы
                $mainKey = $related->getKeyName();
                $mainKey = isset($config['key']) ? $config['key'] : $mainKey;
                $values[]=$mainKey;
                $order = [];
                if ( !isset($config['sort']) ){
                    $order[$key]='ASC';
                }elseif( is_string($config['sort']) ){
                    $order[$config['sort']]='ASC';
                }elseif( is_array($config['sort']) ){
                    die('сортировка по множественным параметрам не реализована');
                }



                $sql = $related;
                foreach( $order as $orderKey=>$type ){
                    $sql->orderBy($orderKey,$type);
                }
                $items = $sql->get();


                foreach($items as $item ){

                    $selected = in_array($item->$mainKey,$select) ? 'selected="selected"' : '';
                    $value = $config['value'];
                    foreach($values as $sqlValue ){
                        $value = str_replace('{'.$sqlValue.'}',$item->$sqlValue,$value);
                    }
                    $data.='<option '.$selected.' value="'.htmlspecialchars($item->$mainKey).'">'.htmlspecialchars($value).'</option>';
                }

                //$related->
            }elseif (  $f instanceof \Illuminate\Database\Eloquent\Relations\belongsToMany  ){
                //  связь многие ко многим

                //  получаем модель связанную

            $related = $f->getRelated();
            $table = $f->getTable();
            $key = $related->getForeignKey();
            $values[]=$key;
            $order = [];
            if ( !isset($config['sort']) ){
                $order[$key]='ASC';
            }elseif( is_string($config['sort']) ){
                $order[$config['sort']]='ASC';
            }elseif( is_array($config['sort']) ){
                die('сортировка по множественным параметрам не реализована');
            }
            $sql = $related;
            foreach( $order as $orderKey=>$type ){
                $sql->orderBy($orderKey,$type);
            }
            $items = $sql->get($values);

            foreach($items as $item ){
                $selected = in_array($item->$key,$select) ? 'selected="selected"' : '';
                $value = $config['value'];
                foreach($values as $sqlValue ){
                    $value = str_replace('{'.$sqlValue.'}',$item->$sqlValue,$value);
                }
                $data.='<option '.$selected.' value="'.htmlspecialchars($item->$key).'">'.htmlspecialchars($value).'</option>';
            }

            //$related->
            }
        }
        return $data;
    }

    /**
     * Проверяем отправку формы
     */
    public function isSubmit(){
        return !is_null($this->getResponseData( $this->nameForm.'_formbuildersubmit') ) ? true : false;
    }

    /**
     *
     */
    function save(){

        $id = $this->getResponseData( $this->nameForm.'_formbuilderid' );

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

        switch($this->config['method']){
            case('post'):
                return (isset($_POST[$key])) ? $_POST[$key] : null;
                break;
            case('get'):
                return (isset($_GET[$key])) ? $_GET[$key] : null;
                break;
        }
    }

    protected function getModelData($key){
        if ( !isset($this->config['model']) ){
            return null;
        }
        if ( false===$this->model ){
            $model =  $this->config['model'];
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
        return $this->model->$key;

    }




    public function getData($key){
        if ( $this->isSubmit() ){
            return $this->getResponseData($key);
        }else{
            return $this->getModelData($key);
        }

    }

        //  inline method

    protected function setLine($str){
        return $str."\n";
    }
    protected function clear($str){
        return strtolower(preg_replace('#[^a-z]#i','',$str));

    }

    public function cssRender(){
        $result = '';
        foreach( self::$assetCss as $file=>$ext ){
            if ( is_null($ext) ){
                $result .= \HTML::style(asset('packages/nifus/formbuilder/'.$file.'.css'));
            }else{
                $result .= \HTML::style(asset('packages/nifus/formbuilder/'.$ext.'/'.$file.'.css'));

            }
        }
       // $result.=self::$assetCss;
        return $result;
    }

    public function jsRender(){
        $result = '';

        foreach( self::$assetJs as $file=>$ext ){
            if ( is_null($ext) ){
                $result .= \HTML::script(asset('packages/nifus/formbuilder/'.$file.'.js'));
                self::$assetJs[$file]=false;
            }elseif(false===$ext){
                continue;
            }else{
                $result .= \HTML::script(asset('packages/nifus/formbuilder/'.$ext.'/'.$file.'.js'));
                self::$assetJs[$file]=false;
            }
        }
        $result.=self::$staticJs."
<script>$(document).ready(function() {
   $('#".$this->nameForm."').append($('<input type=\"hidden\" name=\"".$this->nameForm."_formbuildersubmit\" value=\"1\">'));
   ";
        if ( false!=$this->modelKey ){
            $result.="$('#".$this->nameForm."').append($('<input type=\"hidden\" name=\"".$this->nameForm."_formbuilderid\" value=\"".$this->modelKey."\">'));";
        }
        $result.="
});
</script>";
        return $result;
    }

    static function jsAdd($file,$ext=null){
        if ( isset(self::$assetJs[$file]) ){
            return false;
        }
        self::$assetJs[$file]=$ext;
    }

    static function cssAdd($file,$ext=null){
        if ( isset(self::$assetCss[$file]) ){
            return false;
        }
        self::$assetCss[$file]=$ext;
    }
    static function setJs($js,$path=null){
        if ( is_null($path) ){
            self::$staticJs.="\n\r".$js."\n\r";
        }else{
            self::$staticJs.="\n\r"."<!-- include " . $path . "--> \n\r" . $js ."\n\r";
        }
    }



}