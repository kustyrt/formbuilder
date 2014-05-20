<?php

namespace Nifus\FormBuilder\Fields;

class One2More extends \Nifus\FormBuilder\Fields{

    protected $form;
    public function __construct($typeField,$name='', array $config,$builder){
        parent::__construct($typeField,$name, $config,$builder);
        \Nifus\FormBuilder\Render::jsAdd('One2More','One2More');

    }

    static function delete(){
        $class = \Input::get('class');
        $method = \Input::get('method');
        $id = \Input::get('id');
        $instance = new $class;
        $relation = $instance->$method();
        $model = $relation->getRelated();
        $delete_object = $model::find($id);
        if ( !is_null($delete_object) ){
            $delete_object->delete();
        }
        return \Response::json(['message'=>'ok']);
    }

    static function edit(){
        $class = \Input::get('class');
        $method = \Input::get('method');
        $id = \Input::get('id');
        $fields = explode(',',\Input::get('fields'));
        $instance = new $class;
        $relation = $instance->$method();
        $model = $relation->getRelated();
        $edit_object = $model::find($id);
        $result = [];
        if ( !is_null($edit_object) ){
            foreach( $fields as $field ){
                $result[$field]=$edit_object->$field;
            }
        }
        return \Response::json($result);
    }

    public function setValues($values){
        $this->config['values']=$values;
        return $this;
    }

    public function setFields($fields){
        $this->config['fields'] = $fields;
        return $this;
    }

    public function renderWithOutForm($response){

        $rows = $this->__getData();
        $v = \View::make('formbuilder::classes/fields/One2More/js')
            ->with('cols',  json_encode($this->config['values']) )
            ->with('data',  json_encode($rows) )
            ->with('id_form', $this->config['name']);
        \Nifus\FormBuilder\Render::setJs($v->render(), $v->getPath());

        $this->configFields();
        $names = $this->getNamesFields();

        $fields = $this->config['fields'];
        $this->config['form']= \Nifus\FormBuilder\FormBuilder::create($this->config['name'])->setRender('array')
            ->setFields($fields);
        $render_views = $this->config['form']->render($fields,true);
        $form = ($render_views->bootstrap3Render($names));

        return '<div  class="modal fade" id="modal_sub_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" id="modal_'.$this->config['name'].'">
              <div class="modal-content">
                <form  method="post" id="sbt_form" data-toggle="validator">

                <div class="modal-body"><div class="row">'.$form.'</div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                      <button type="submit" class="btn btn-primary" data-action="save" data-elements="'.$this->config['name'].'">Сохранить</button>
                    </div>
                </div>
                </form>
                </div>
          </div>
        </div>';
    }

    public function renderElement($response){


        return '
            <div><div class="container" id="container_'.$this->config['name'].'"></div></div>
            <button type="button" class="btn btn-primary" data-action="create">Добавить</button>
         ';
    }

    private function __getData(){
        $data = $this->config['data'];
        $method = $data['method'];
        $model = $this->builder->model;
        $rows = [];
        $id = $this->builder->getId();
        if ( false!==$id ){
            $instance = $model::find($id);
            $result = $instance->$method()->get();
            $values = $this->config['values'];
            $i=0;
            foreach( $result as $row ){
                $j=0;
                foreach( $values as $key=>$label ){
                    $rows[$i][$j] = ['value'=>$row->$key,'name'=>$key,'label'=>$label];
                    $j++;
                }
                $i++;
            }
        }
        return $rows;
    }

    private function configFields(){
        $name_main_form = ($this->builder->form_name);
        $name_sub_form = ($this->config['name']);
        $fields = $this->config['fields'];
        foreach($fields as $field ){
            $field->name = $field->name;
            $field->set('data-name',$field->name);
            $field->set('data-form',$name_sub_form);
            $field->set('data-label',$field->label);
        }
    }

    private function getNamesFields(){
        $names=[];
        $fields = $this->config['fields'];
        foreach($fields as $field ){
            $name= $field->name;
            $names[]=$name;
        }
        return $names;
    }

}