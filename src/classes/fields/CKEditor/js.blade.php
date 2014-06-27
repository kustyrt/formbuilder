<script>

    $(function () {
            CKEDITOR.replace( '{{$id}}',{
                language : 'ru',
                extraPlugins : 'base64image',
                //toolbar : 'Basic',
                @if ( isset($config['toolbar']) )
                    toolbar :{{$config['toolbar']}}
                @endif
            } );
            CKEDITOR.editorConfig = function( config ) {


                /*
                config.toolbar = [
                    [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ],
                    [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
                    '/',
                    [ 'Bold', 'Italic' ]
                ];*/
            };

        $('#edit_save_button').click(function(){
            for ( instance in CKEDITOR.instances ){
                CKEDITOR.instances[instance].updateElement();
            }
        })

    });
</script>