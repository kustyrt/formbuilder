<script>

    $(function () {

            CKEDITOR.replace( '{{$id}}' );
            CKEDITOR.editorConfig = function( config ) {
                config.language = 'ru';
                config.extraPlugins = 'base64image';
            };

        $('#edit_save_button').click(function(){
            for ( instance in CKEDITOR.instances ){
                CKEDITOR.instances[instance].updateElement();
            }
        })

    });
</script>