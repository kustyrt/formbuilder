
$(function () {

    $('a[data-delete-file]').on('click',function(){

        var file = $(this).attr('data-delete-file');
        var path = $(this).attr('data-path-file');
        var hash = $(this).attr('data-hash-file');
        var group = $(this).parents('div.control-group');
        group.find('div.row').hide();
        group.find('input[type=file]').show();
        $.ajax({
            type: "POST",
            url: '/formbuilder/file/delete',
            data: "file="+file+"&path="+path+"&hash="+hash,
            dataType: "json",
            success: function(){

            }
        });

    })

});