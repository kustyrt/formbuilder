<script>

    $(function () {

            CKEDITOR.replace( '{{$id}}' );
            CKEDITOR.editorConfig = function( config ) {
                config.language = 'ru';

                config.extraPlugins = 'base64image';

            };
    });
</script>