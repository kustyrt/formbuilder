<?php

namespace Nifus\FormBuilder\Fields;

class One2more extends \Nifus\FormBuilder\Fields{



    public function setFields($fields){
        $form = \Nifus\FormBuilder\FormBuilder::create('news_posts')
            ->setModel('\News\Post')
            ->setLibrary(['jquery','bootstrap'])
            ->setRender('bootstrap3',['grid'=>'col-md-6'])->setExtensions(['Placeholder'])
            ->setFields([
                \Nifus\FormBuilder\FormBuilder::createField('text')
                    ->setName('title')->setLabel( 'Заголовок' )
                    ->setClass('form-control')->setValid(),
                \Nifus\FormBuilder\FormBuilder::createField('date')
                    ->setName('time_add')->setLabel( 'Дата добавления' )->setFormat('yyyy-mm-dd')
                    ->setClass('form-control'),

                \Nifus\FormBuilder\FormBuilder::createField('textarea')
                    ->setName('short_desc')->setLabel( 'Краткое описание' )
                    ->setClass('form-control')->setValid( ),

                \Nifus\FormBuilder\FormBuilder::createField('wysiwyg')
                    ->setName('desc')->setLabel( 'Полное описание' )->setId('summernote')
                    ->setClass('form-control'),
            ],'Общие');
        return $this;
    }

    public function renderElement($response){
        return '<div>111</div>';
    }

}