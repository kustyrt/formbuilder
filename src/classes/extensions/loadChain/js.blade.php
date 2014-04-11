<script type="text/javascript">
    var Chain={
        init:function(form){
            this.form = $('#'+form);
            this.source = $('#'+form).find('*[data-source]');
            this.receiver = $('#'+form).find('*[name='+this.source.attr('data-receiver')+']');

            $('body').on('change',this.source.attr('id'),function()
            {

                Chain.receiver.attr('disabled','disabled');
                var id = Chain.source.val();
                var url = Chain.source.attr('data-url');

                $.ajax({
                    dataType: Chain.source.attr('data-format'),
                    type: "POST",
                    url: url,
                    data: { id: id }
                }).done(function( data ) {
                        var select = '';
                        for (var i = 0; i < data.length; i++) {
                            select += '<option value="' + data[i].city_id + '">' +
                                data[i].title_ru + '</option>';
                        }
                        Chain.receiver.html(select);
                        Chain.receiver.removeAttr('disabled');
                });

            })
            this.source.trigger('change');

        }
    }
    $(document).ready(function() {
        Chain.init('{{$id_form}}');

    });
</script>


