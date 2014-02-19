<?php

namespace Nifus\FormBuilder;

/**
 * Класс отвечает за отображение формы
 *
 * Class Render
 * @package Nifus\FormBuilder
 */
class Render{
    static
        $assetJs = [
            'jquery' => false,
            //'require'=>null,
            'engine'=>null
        ],
        $assetCss = [],
        $staticJs = '';

    protected
        $config=[];

    private
        $builder = false;


    public function __construct($config,$builder){

        $this->config = $config;
        $this->builder = $builder;

        self::$assetJs['jquery'] = (false===(self::$assetJs['jquery'])) && !isset($this->config['jquery']) ? false : null;
        if ( isset($this->config['ajax'])  ){
            self::jsAdd('jquery.form');
        }
        $this->loadExtensions(  );
    }

    function getConfig(){
        return $this->config;
    }


    function render(){

        switch($this->config['render']['format']){
            case('table'):
                return
                    $this->formRender($this->tableRender()).
                    $this->cssRender().
                    $this->jsRender();
                break;
            case('ul'):
                break;
            case('p'):
                $this->formRender($this->paragrafRender()).
                $this->cssRender().
                $this->jsRender();
                break;
            case('dev'):
                break;
            case('array'):
                $view = new RenderView($this->arrayRender(),$this->jsRender(),$this->cssRender());

                return $view;
                    /*[
                    'fields' => $this->arrayRender(),
                    'js'=>,
                    'css'=>,
                    //'errors'=>$this->errors(),
                ];*/
                break;
            default:
                throw new ConfigException(' Неправильный формат вывода '.$this->config['render']['format'] );
                break;
        }
    }


    /**
     * Загружаем расширения
     */
    protected function loadExtensions(){
        if ( !isset($this->config['extensions']) ){
            return false;
        }
        foreach( $this->config['extensions'] as $ext ){
            $class = 'Nifus\FormBuilder\Extensions\\'.$ext;
            if ( !class_exists($class) ){
                throw new RenderException('Не найден класс '.$class);
            }
            $ext = new $class($this->config,$this->builder,$this);
            $ext->loadAsset();
            //$class::autoload($this);
        }
        return true;
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
   $('#".$this->builder->getNameForm()."').append($('<input type=\"hidden\" name=\"".$this->builder->getNameForm()."_formbuildersubmit\" value=\"1\">'));
   ";
        /*
        if ( false!=$this->modelKey ){
            $result.="$('#".$this->nameForm."').append($('<input type=\"hidden\" name=\"".$this->nameForm."_formbuilderid\" value=\"".$this->modelKey."\">'));";
        }*/
        $result.="
});
</script>";
        self::$staticJs = '';
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


    protected function formRender($content){

        $form=$this->setLine('<form enctype="multipart/form-data" method="'.$this->config['method'].'" id="'.$this->builder->getNameForm().'">');
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



    protected function paragrafRender(){
        $par = '';
        foreach( $this->config['fields'] as $name=>$config){
            $elementRender = $this->elementRender($name,$config);
            $par.=$this->setLine('<p class="'.$name.'">');
            $par.=$this->setLine($elementRender['label'].'');
            $par.=$this->setLine('</p>');
            $par.=$this->setLine('<p>');
            $par.=$this->setLine($elementRender['element']);
            $par.=$this->setLine('</p>');
        }
        $par.=$this->setLine('<p><input type="submit" /></p>');


        return $par;
    }

    /**
     * @param $name
     * @param $config
     * @return array
     * @throws RenderException
     */
    protected function elementRender($name,$config){
        $response = $this->builder->getResponse();


        $class = 'Nifus\FormBuilder\Fields\\'.ucfirst($config['type']);
        if ( !class_exists($class) ){
            throw new RenderException('Не найден класс '.$class);
        }
        $element = new $class($name,$config);
        return ['label'=>$element->renderLabel(),'element'=>$element->renderElement($response)];
        /*
        switch($config['type']){


            case('file'):
                $value = $response->getData($name);
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
                    $multi = 'multiple="multiple" size="5"';
                }
                return [
                    'label'=>'<label for="'.$config['id'].'"  name="'.$name.'">'.$config['label'].'</label>',
                    'element'=>'<select '.$multi.' name="'.$config['name'].'" id="'.$config['id'].'" '.$attrs.'>'.$data.'</select>'
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
        return [];*/
    }

    function isMultiple($config){
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
                $selected = in_array($key,$select) ? 'selected="selected"' : '';
                $data.='<option '.$selected.' value="'.htmlspecialchars($key).'">'.htmlspecialchars($value).'</option>';
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
                $items = $sql->get($values);


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




    //  inline method

    protected function setLine($str){
        return $str."\n";
    }







}