<?php
namespace Nifus\FormBuilder;


class UploadExtension{

        //  вызывается
    function upload(){

        $file = \Input::file($_POST['name']);
        $size = $file->getSize();
        $realName = $file->getClientOriginalName();
        \Input::file($_POST['name'])->move($_POST['path'],$realName);
        return \Response::json(
            array(
                'files'=>[
                    [
                    "thumbnailUrl"=> "http:\/\/example.org\/files\/thumbnail\/picture1.jpg",

                    'url'=>'/media/catalog/post/upload/'.$realName,
                    'deleteType' => 'DELETE',
                    'deleteUrl' => route('formBuilder',['ext'=>'Upload','action'=>'upload','file'=>$realName]),
                    'name' => $realName,
                    'size' => $size
                    ]
                ]

            )
        );

        $upload_handler = new \UploadHandler([
            'script_url'=>route('formBuilder',['ext'=>'Upload','action'=>'upload']),
            'upload_dir'=>$_POST['path'],
            'upload_url'=>$_POST['url'],
        ]);
        exit();

    }
    /**
     * Подключаем в начале
     *
     * @param $formBuilder
     */
    static function autoload($formBuilder){
        FormBuilder::jsAdd('jquery');
        FormBuilder::jsAdd('js/vendor/jquery.ui.widget','upload');
        FormBuilder::jsAdd('js/jquery.iframe-transport','upload');
        FormBuilder::jsAdd('js/jquery.fileupload','upload');
    }

    static function element($name,$config,$formBuilder){

        $v = \View::make('formbuilder::extends/upload/js')
            ->with('id','result_'.$config['id'])
            ->with('path',public_path().$config['path'])
            ->with('url','http:/'.$config['path'])
            ->with('name_file',$config['name'])
            ->with('name',$config['id']);

        $formBuilder->setJs(  $v->render(),$v->getPath() );
        //imageUpload
        return [
            'label'=>'<label for="'.$config['id'].'"  name="'.$name.'">'.$config['label'].'</label>',
            'element'=>'<input id="'.$config['id'].'" type="file" name="'.$config['name'].'" data-url="'.route('formBuilder',['ext'=>'Upload','action'=>'upload']).'" multiple><div id="result_'.$config['id'].'"></div>'
        ];
    }


    static function fields($config){

        if ( isset($config['type']) &&  $config['type']=='fileUpload'  ){

            return ['data-mask'=>$config['mask']];
        }

    }
}