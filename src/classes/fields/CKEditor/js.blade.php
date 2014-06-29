<script>

    $(function () {
            CKEDITOR.replace( '{{$id}}',{
                language : 'ru',
                extraPlugins : 'base64image',
                //toolbar : 'Basic',
                @if ( isset($config['toolbar'])  )
                    toolbar :{{$config['toolbar']}}
                @else
                    toolbar : 'Full'
                @endif
            } );


        $('#edit_save_button').click(function(){
            for ( instance in CKEDITOR.instances ){
                CKEDITOR.instances[instance].updateElement();
            }
        })

    });
</script>