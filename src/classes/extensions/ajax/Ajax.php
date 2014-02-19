<?php
namespace Nifus\FormBuilder\Extensions;

use \Nifus\FormBuilder\Extension as Extension;

class Ajax extends Extension
{

    static function form()
    {
        return true;
    }

    public function loadAsset()
    {

        \Nifus\FormBuilder\Render::jsAdd('jquery');
        //\Nifus\FormBuilder\Render::jsAdd('jquery.form','ajax');

        // валидация
        \Nifus\FormBuilder\Render::jsAdd('jquery.validate.min', 'validate');
        $lang = \App::getLocale();
        if ($lang != 'en') {
            \Nifus\FormBuilder\Render::jsAdd('languages/validettaLang-' . \App::getLocale(), 'validate');
        }
        \Nifus\FormBuilder\Render::cssAdd('validetta', 'validate');


        $config = $this->builder->getConfig('ajax');
        if (!isset($config) || !is_array($config)) {
            return false;
        }


        $v = \View::make('formbuilder::classes/extensions/ajax/js')
            ->with('formName', $this->builder->getNameForm())
            ->with('formName', $this->builder->getNameForm())
            ->with('formAction', $config['url']);
        $this->render->setJs($v->render(), $v->getPath());


    }

    public function configField($config)
    {
        $result = '';
        if (!isset($config['required'])) {
            return [];
        }
        $result .= 'required,';

        $types = explode(';', $config['required']);

        foreach ($types as $t) {
            $t = explode(':', $t);
            switch ($t[0]) {
                case('min'):
                    $result .= 'minLength[' . $t[1] . '],';
                    break;
                case('max'):
                    $result .= 'maxLength[' . $t[1] . '],';
                    break;
                case('type'):
                    $result .= '' . $t[1] . ',';
                    break;
            }
        }
        return ['data-validetta' => $result,'required'=>null];

    }
}