<script>
    $(function () {
        $('#{{$name}}').fileupload({
            dataType: 'json',
            formData:[
                {'name':'path','value':'{{$path}}'} ,
                {'name':'url','value':'{{$url}}'},
                {'name':'name','value':'{{$name_file}}'}
            ],
            done: function (e, data) {

                $.each(data.result.files, function (index, file) {

                    $( '<p><a href="'+file.delete_url+'"><img style="width:100px" src="'+file.url+'"/></a></p>').appendTo( $('#{{$id}}'));
                });
            }

        });
    });
</script>
