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
    protected
        $config = [];
    private
        $builder = false;

    public function __construct($config, $builder)
    {

        $this->config = $config;
        $this->builder = $builder;

        self::$assetJs['jquery'] = (false === (self::$assetJs['jquery'])) && !isset($this->config['jquery']) ? false : null;
        if (isset($this->config['ajax'])) {
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
        if (!isset($this->config['extensions'])) {
            return false;
        }
        foreach ($this->config['extensions'] as $ext) {
            $class = 'Nifus\FormBuilder\Extensions\\' . $ext;
            if (!class_exists($class)) {
                throw new RenderException('Не найден класс ' . $class);
            }
            $ext = new $class($this->config, $this->builder, $this);
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

    function getConfig()
    {
        return $this->config;
    }

    function render($fields = array())
    {

        switch ($this->config['render']['format']) {
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
                $this->formRender($this->paragrafRender()) .
                $this->cssRender() .
                $this->jsRender();
                break;
            case('dev'):
                break;
            case('array'):
                $view = new RenderView($this->arrayRender(), $this->jsRender(), $this->cssRender());

                return $view;

                break;
            default:
                throw new ConfigException(' Неправильный формат вывода ' . $this->config['render']['format']);
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
     * @return array
     * @throws RenderException
     */
    protected function elementRender($name, $config)
    {
        $response = $this->builder->getResponse();


        $class = 'Nifus\FormBuilder\Fields\\' . ucfirst($config['type']);
        if (!class_exists($class)) {
            throw new RenderException('Не найден класс ' . $class);
        }
        $element = new $class($name, $config);
        return ['label' => $element->renderLabel(), 'element' => $element->renderElement($response)];

    }

    protected function formRender($content)
    {
        $formAttrs = [
            'enctype' => $this->config['enctype'],
            'method' => $this->config['method'],
            'id' => $this->builder->getNameForm(),

        ];
        if (isset($this->config['action'])) {
            $formAttrs['url'] = $this->config['action'];
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
   $('#" . $this->builder->getNameForm() . "').append($('<input type=\"hidden\" name=\"" . $this->builder->getNameForm() . "_formbuildersubmit\" value=\"1\">'));
   ";
        /*
        if ( false!=$this->modelKey ){
            $result.="$('#".$this->nameForm."').append($('<input type=\"hidden\" name=\"".$this->nameForm."_formbuilderid\" value=\"".$this->modelKey."\">'));";
        }*/
        $result .= "
});
</script>";
        self::$staticJs = '';
        return $result;
    }

    protected function tableRender()
    {
        $table = $this->setLine('<table class="formBuilder">');
        foreach ($this->config['fields'] as $name => $config) {
            $elementRender = $this->elementRender($name, $config);
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
        $par = '';
        foreach ($this->config['fields'] as $name => $config) {
            if (!in_array($name, $fields)) {
                continue;
            }
            $elementRender = $this->elementRender($name, $config);
            $par .= $this->setLine('<p class="' . $name . '">');
            $par .= $this->setLine($elementRender['label'] . '');
            $par .= $this->setLine('</p>');
            $par .= $this->setLine('<p>');
            $par .= $this->setLine($elementRender['element']);
            $par .= $this->setLine('</p>');
        }
        $par .= $this->setLine('<p><input type="submit" /></p>');
        return $par;
    }

    protected function bootstrap3Render($fields = array())
    {
        $table = '';

        foreach ($this->config['fields'] as $name => $config) {
            if (!in_array($name, $fields)) {
                continue;
            }
            $table .= $this->setLine('<div class="col-md-6">');
            $elementRender = $this->elementRender($name, $config);
            if ( is_array($elementRender['element']) ){
                // 4 checkbox &&  radio
                $table .= $this->setLine($elementRender['label']);

                $table .= $this->setLine('<div>');

                foreach( $elementRender['element'] as $i=>$element ){
                    $table .= $this->setLine('<div class="radio"><label class="checkbox-inline">
');
                    $table .= $this->setLine($elementRender['element'][$i]);
                    $table .= $this->setLine('</label></div>');

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
        foreach ($this->config['fields'] as $name => $config) {
            $elements[$name] = $this->elementRender($name, $config);
        }
        return $elements;
    }


}