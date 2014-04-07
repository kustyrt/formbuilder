<?php

namespace Nifus\FormBuilder;

/**
 * Класс отвечает за отображение формы
 *
 * Class Render
 * @package Nifus\FormBuilder
 */
class Render
{
    static
        $assetJs = [
        'jquery' => false,
        //'require'=>null,
        //'engine'=>null
    ],
        $assetCss = [],
        $staticJs = '';

    private
        $fields,
        $builder,
        $response;

    public function __construct($builder,$response)
    {

        $this->builder = $builder;
        $this->response = $response;

        self::$assetJs['jquery'] = (false === (self::$assetJs['jquery'])) && !isset($this->builder->jquery) ? false : null;
        if (isset($this->builder->ajax)) {
            self::jsAdd('jquery.form');
        }
        $this->loadExtensions();
    }

    static function jsAdd($file, $ext = null)
    {
        if (isset(self::$assetJs[$file])) {
            return false;
        }
        self::$assetJs[$file] = $ext;
    }

    /**
     * Загружаем расширения
     */
    protected function loadExtensions()
    {

        if ( !is_array($this->builder->extensions)) {
            return false;
        }

        foreach ($this->builder->extensions as $ext) {
            $class = 'Nifus\FormBuilder\Extensions\\' . $ext;
            if (!class_exists($class)) {
                throw new RenderException('Не найден класс ' . $class);
            }
            $ext = new $class($this->builder);
            $ext->loadAsset();
            //$class::autoload($this);
        }
        return true;
    }

    static function cssAdd($file, $ext = null)
    {
        if (isset(self::$assetCss[$file])) {
            return false;
        }
        self::$assetCss[$file] = $ext;
    }

    static function setJs($js, $path = null)
    {
        if (is_null($path)) {
            self::$staticJs .= "\n\r" . $js . "\n\r";
        } else {
            self::$staticJs .= "\n\r" . "<!-- include " . $path . "--> \n\r" . $js . "\n\r";
        }
    }



    function render($fields = array())
    {
        $render_config = $this->builder->render;
        switch ($render_config['format']) {
            case('bootstrap3'):
                if (sizeof($fields) > 0) {
                    return
                        $this->bootstrap3Render($fields);
                } else {
                    return
                        $this->formRender($this->bootstrap3Render()) .
                        $this->cssRender() .
                        $this->jsRender();
                }

                break;

            case('table'):
                return
                    $this->formRender($this->tableRender()) .
                    $this->cssRender() .
                    $this->jsRender();
                break;
            case('ul'):
                break;
            case('p'):
                if (sizeof($fields) > 0) {
                    return
                        $this->paragrafRender($fields);
                } else {
                    return
                        $this->formRender($this->paragrafRender()) .
                        $this->cssRender() .
                        $this->jsRender();
                }

                break;
            case('dev'):
                break;
            case('array'):
                $view = new RenderView($this->arrayRender(), $this->jsRender(), $this->cssRender(), $this->errors());

                return $view;

                break;
            default:
                throw new ConfigException(' Неправильный формат вывода ' . $render_config['format']);
                break;
        }
    }



    protected function setLine($str)
    {
        return $str . "\n";
    }

    /**
     * @param $name
     * @param $config
     * @param $type
     * @return array
     * @throws RenderException
     */
    protected function elementRender($name, $config,$type)
    {
        $class = 'Nifus\FormBuilder\Fields\\' . ucfirst($type);
        if (!class_exists($class)) {
            throw new RenderException('Не найден класс ' . $class);
        }
        $element = new $class($type,$name, $config,$this->builder);

        return ['label' => $element->renderLabel(), 'element' => $element->renderElement($this->response)];

    }

    protected function formRender($content)
    {

        $formAttrs = [
            'enctype' => $this->builder->enctype,
            'method' => $this->builder->method,
            'id' => $this->builder->form_name,

        ];
        $action = $this->builder->action;
        if (isset($action)) {
            $formAttrs['url'] = $action;
        }

        return \Form::open($formAttrs) . $content . \Form::close();;
    }

    public function cssRender()
    {
        $result = '';
        foreach (self::$assetCss as $file => $ext) {
            if (is_null($ext)) {
                $result .= \HTML::style(asset('packages/nifus/formbuilder/' . $file . '.css'));
            } else {
                $result .= \HTML::style(asset('packages/nifus/formbuilder/' . $ext . '/' . $file . '.css'));

            }
        }
        // $result.=self::$assetCss;
        return $result;
    }

    public function jsRender()
    {
        $result = '';

        foreach (self::$assetJs as $file => $ext) {
            if (is_null($ext)) {
                $result .= \HTML::script(asset('packages/nifus/formbuilder/' . $file . '.js'));
                self::$assetJs[$file] = false;
            } elseif (false === $ext) {
                continue;
            } else {
                $result .= \HTML::script(asset('packages/nifus/formbuilder/' . $ext . '/' . $file . '.js'));
                self::$assetJs[$file] = false;
            }
        }
        $result .= self::$staticJs . "
<script>$(document).ready(function() {
   $('#" . $this->builder->form_name . "').append($('<input type=\"hidden\" name=\"" . $this->builder->form_name . "_formbuildersubmit\" value=\"1\">'));
   ";

        if ( false!=$this->builder->model_key_value ){
            $result.="$('#".$this->builder->form_name."').append($('<input type=\"hidden\" name=\"".$this->builder->form_name."_formbuilderid\" value=\"".$this->builder->model_key_value."\">'));";
        }
        $result .= "
});
</script>";
        self::$staticJs = '';
        return $result;
    }

    protected function tableRender()
    {
        $table = $this->setLine('<table class="formBuilder">');
        foreach ($this->fields as $name => $config) {
            $type = $config['type'];
            $config = $config['config'];
            $elementRender = $this->elementRender($name, $config,$type);
            $table .= $this->setLine('<tr class="' . $name . '">');
            $table .= $this->setLine('<td>');
            $table .= $this->setLine($elementRender['label'] . '');
            $table .= $this->setLine('</td>');
            $table .= $this->setLine('<td>');
            $table .= $this->setLine($elementRender['element']);
            $table .= $this->setLine('</td>');
            $table .= $this->setLine('</tr>');
        }
        $table .= $this->setLine('<tr>');
        $table .= $this->setLine('<td colspan="2"><input type="submit" /></td>');
        $table .= $this->setLine('</tr>');
        $table .= $this->setLine('</table>');


        return $table;
    }

    protected function paragrafRender($fields = array())
    {
        $show_label = isset($this->config['render']['label']) ? $this->config['render']['label'] : true;
        $par = '';
        foreach ($this->fields as $name => $config) {
            if (!in_array($name, $fields)) {
                continue;
            }
            $type = $config['type'];
            $config = $config['config'];
            $elementRender = $this->elementRender($name, $config,$type);


            if ( true===$show_label && (!isset($config['inline']) || false===$config['inline'])){
                $par .= $this->setLine('<p class="' . $name . '">');
                $par .= $this->setLine($elementRender['label'] . '');
                $par .= $this->setLine('</p>');
            }
            if ( !isset($config['inline']) || false===$config['inline'] ){
                $par .= $this->setLine('<p>');
                $par .= $this->setLine($elementRender['element']);
                $par .= $this->setLine('</p>');
            }else{
                $par .= $this->setLine($elementRender['element']);

            }
        }
        if ( sizeof($fields)==0 ){
            $par .= $this->setLine('<p><input type="submit" /></p>');
        }
        return $par;
    }

    protected function bootstrap3Render($fields = array())
    {
        $table = '';
        foreach ($this->fields as $name => $config) {
            if ( sizeof($fields)>0 && !in_array($name, $fields)) {
                continue;
            }
            $table .= $this->setLine('<div class="col-md-6">');
            $type = $config['type'];
            $config = $config['config'];
            $elementRender = $this->elementRender($name, $config,$type);

            if ( is_array($elementRender['element']) ){
                // 4 checkbox &&  radio
                $table .= $this->setLine($elementRender['label']);
                $table .= $this->setLine('<div>');
                foreach( $elementRender['element'] as $i=>$element ){
                    $table .= $this->setLine('<label class="checkbox-inline">');
                    $table .= $this->setLine($elementRender['element'][$i]);
                    $table .= $this->setLine('</label>');
                }
                $table .= $this->setLine('</div>');
            }else{
                $table .= $this->setLine($elementRender['label']);
                $table .= $this->setLine($elementRender['element']);
            }
            $table .= $this->setLine('</div>');
        }
        return $table;
    }


    //  inline method

    protected function arrayRender()
    {
        $elements = array();
        foreach ($this->fields as $name => $config) {
            $type = $config['type'];
            $config = $config['config'];

            $elements[$name] = $this->elementRender($name, $config,$type);
        }
        return $elements;
    }

    public function setFields(array $fields ){
        $this->fields = $fields;
    }

    protected function errors(){
        return $this->builder->errors();
    }


}