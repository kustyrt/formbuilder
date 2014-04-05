<?php
namespace Nifus\FormBuilder\Extensions;

use \Nifus\FormBuilder\Extension as Extension;

class Validetta extends Extension
{



    public function loadAsset()
    {
        \Nifus\FormBuilder\Render::jsAdd('jquery');

        // валидация
        \Nifus\FormBuilder\Render::jsAdd('jquery.validate.min', 'validate');
        $lang = \App::getLocale();
        if ($lang != 'en') {
            \Nifus\FormBuilder\Render::jsAdd('languages/validettaLang-' . \App::getLocale(), 'validate');
        }
        \Nifus\FormBuilder\Render::cssAdd('validetta', 'validate');





        $v = \View::make('formbuilder::classes/extensions/validetta/js')
            ->with('formName', $this->builder->form_name);
        \Nifus\FormBuilder\Render::setJs($v->render(), $v->getPath());


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