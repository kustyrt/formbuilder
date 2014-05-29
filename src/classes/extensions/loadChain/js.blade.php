<script type="text/javascript">
    var Chain={
        init:function(form){
            var form = $('#'+form);

            var source = form.find('*[data-source]');

            source.on('change',function()
            {
                var id =  $(this).val();
                if ( id==0 ){
                    return false;
                }
                var url =  $(this).attr('data-url');
                var receiver_id= $(this).attr('data-receiver');
                var type = $(this).attr('data-format')
                var  receiver = form.find('*[name='+receiver_id+']');
                Chain.ajax(type,id,receiver,url );
            });


            source.each( function(){
                $(this).trigger('change');
            })
        },

        ajax:function(type,id,receiver,url){
            var receiver = receiver;
            receiver.attr('disabled','disabled');

            $.ajax({
                dataType: type,
                type: "post",
                url: url,
                data: { id: id }
            }).done(function( data ) {
                    var select = '';
                    for (var i = 0; i < data.length; i++) {
                        select += '<option value="' + data[i].city_id + '">' +
                            data[i].title + '</option>';
                    }
                    receiver.html(select).removeAttr('disabled').trigger('change');
            });
        }
    };

    $(document).ready(function() {
        Chain.init('{{$id_form}}');
    });
</script>


