<?php

namespace Nifus\FormBuilder\Fields;

class Select extends \Nifus\FormBuilder\Fields{


    public function setOrder($rules)
    {
        $this->config['data']['order_rules'] = $rules;
        return $this;
    }
    public function setMethod($method)
    {
        $this->config['data']['method'] = $method;
        $this->config['data']['type'] = 'model';
        return $this;
    }
    public function setDefault($default)
    {
        $this->config['data']['default'] = $default;
        return $this;
    }
    public function setSize($size)
    {
        $this->config['data']['size'] = $size;
        return $this;
    }

    public function setOptions(array $options,$type='key_value')
    {
        $this->config['data']['options'] = $options;
        $this->config['data']['type'] = $type;
        return $this;
    }

    public function setValue($value)
    {
        $this->config['data']['value'] = $value;
        return $this;
    }


    public function renderElement($response){
        //\Log::info($response->getData($this->config['name']));
        $attrs = $this->renderAttrs();
        $data = $this->selectDataFormat( $response->getData($this->config['name']) );
        $multi='';
        if ( $this->isMultiple( $this->config['data']) ){
            $size= isset( $this->config['data']['size']) ? $this->config['data']['size'] : 5;
            $multi = 'multiple="multiple" size="'.$size.'"';
        }
        return '<select '.$attrs.' '.$multi.'>'.$data.'</select>';
    }

    protected function getDefaultConfig()
    {
        return [
            'data'=>['type'=>'key_value','value'=>'{{title}}']
        ];
    }


    private function isMultiple($config){
        if ( !isset($config['method']) ){
            return false;
        }
        $model = $this->builder->getConfig('model');
        $object = new $model;
        $f = $object->$config['method']();
        if (  $f instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany  ){
            return true;
        }
        return false;
    }

    private function selectDataFormat($data=null){
        $config = $this->config['data'];
        $select = !is_null($data) ? $data : ( isset($config['default']) ? $config['default'] : null)  ;
        if ( !is_array($select) ){
            $select=[$select];
        }
        $data='';
        $config['type'] = (isset($config['type'])) ? $config['type'] :
            (isset($config['method']) ? 'model' : null );

        switch($config['type']){
            case('value'):
                $data = $this->generateOptionsValue($config['options'],$select);
                break;
            case('key_value'):
                $data = $this->generateOptionsKeyValue($config['options'],$select);
                break;
            case('model'):
                $data = $this->generateOptionsModel($config,$select);
                break;
        }
        return $data;
    }

    private function generateOptionsKeyValue(array $data,array $select){
        $html = '';
        foreach($data as $key=>$value ){
            if ( is_array($value) ){
                $html.='<optgroup  label="'.htmlspecialchars($key).'">';
                foreach($value as $key2=>$value2 ){
                    $selected = in_array($key2,$select) ? 'selected="selected"' : '';
                    $html.='<option '.$selected.' value="'.htmlspecialchars($key2).'">'.htmlspecialchars($value2).'</option>';
                }
                $html.='</optgroup>';
            }else{
                $selected = in_array($key,$select) ? 'selected="selected"' : '';
                $html.='<option '.$selected.' value="'.htmlspecialchars($key).'">'.htmlspecialchars($value).'</option>';
            }
        }
        return $html;
    }

    private function generateOptionsValue(array $data,array $select){
        $html = '';
        foreach($data as $key=>$value ){
            if ( is_array($value) ){
                $html.='<optgroup  label="'.htmlspecialchars($key).'">';
                foreach($value as $key2=>$value2 ){
                    $selected = in_array($key2,$select) ? 'selected="selected"' : '';
                    $html.='<option '.$selected.' value="'.htmlspecialchars($value2).'">'.htmlspecialchars($value2).'</option>';
                }
                $html.='</optgroup>';
            }else{
                $selected = in_array($key,$select) ? 'selected="selected"' : '';
                $html.='<option '.$selected.' value="'.htmlspecialchars($value).'">'.htmlspecialchars($value).'</option>';
            }
        }
        return $html;
    }

    private function generateOptionsModel(array $config,array $select){
        $html = '';

        $model = $this->builder->getConfig('model');
        $object = new $model;
        $f = $object->$config['method']();


        if (  $f instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo  ){
            //  связь один ко многим
            return $this->generateOptionsModelBelongsTo($f,$select);
        }elseif (  $f instanceof \Illuminate\Database\Eloquent\Relations\belongsToMany  ){
            //  связь многие ко многим
            return $this->generateOptionsModelBelongsToMany($f,$select);
        }

        return $html;
    }


    private function generateOptionsModelBelongsTo($object,$select){
        $config = $this->config['data'];
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
        $html = '';
        //  получаем модель связанную
        $related = $object->getRelated();
        $key = $related->getForeignKey();   //  ключ связь для основной таблицы
        $mainKey = $related->getKeyName();
        $values[]=$mainKey;
        $order = [];
        $sql = $related;
        if ( isset($config['order_rules']) ){
            foreach( $config['order_rules'] as $orderKey=>$type ){
                $sql = $sql->orderBy($orderKey,$type);
            }
        }
        $items = $sql->get($values);

        foreach($items as $item ){
            $selected = in_array($item->$mainKey,$select) ? 'selected="selected"' : '';
            $value = $this->config['data']['value'];

            foreach($values as $sqlValue ){

                $value = str_replace('{'.$sqlValue.'}',$item->$sqlValue,$value);
            }
            $html.='<option '.$selected.' value="'.htmlspecialchars($item->$mainKey).'">'.htmlspecialchars($value).'</option>';
        }
        return $html;
    }

    private function generateOptionsModelBelongsToMany($object,$select){
        $html = '';
        //  получаем модель связанную
        $related = $object->getRelated();
        $table = $object->getTable();
        $key = $related->getForeignKey();
        $values[]=$key;
        $order = [];

        $sql = $related;
        if ( isset($config['order_rules']) ){
            foreach( $config['order_rules'] as $orderKey=>$type ){
                $sql->orderBy($orderKey,$type);
            }
        }

        $items = $sql->get($values);

        foreach($items as $item ){
            $selected = in_array($item->$key,$select) ? 'selected="selected"' : '';
            $value = $config['value'];
            foreach($values as $sqlValue ){
                $value = str_replace('{'.$sqlValue.'}',$item->$sqlValue,$value);
            }
            $html.='<option '.$selected.' value="'.htmlspecialchars($item->$key).'">'.htmlspecialchars($value).'</option>';
        }
        return $html;
    }


}