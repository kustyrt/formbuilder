<script type="text/javascript">
    $('#{{$formName}}Message').hide().addClass('hide');

    {{$formName}} = {
        send:false,
        ini:function(){
            {{$formName}}.validate();
            {{$formName}}.ajax();
        },
        validate:function(){
            $("#{{$formName}}").validetta({
                realTime     : true
            });
        },
        ajax:function(){
            var options = {

                @if( !empty($formAction) )
                'url': '{{$formAction}}',
                @endif
                'dataType': 'json',
                'type': 'post',
                'success': function (json) {
                if (json && json.error) {
                        $('#{{$formName}}Message').html(json.error).show().removeClass('hide');
                        $('#{{$formName}} [name=' + json.field + ']').focus();
                    } else if (json && json.msg) {
                        $('#{{$formName}}Message').html(json.msg).show().removeClass('hide');
                    } else if (json && json.url) {
                       window.location = json.url;
                    } else {
                        window.location.reload(true);
                    }
                },
                'error': function (event, jqXHR, ajaxSettings) {
                    $('#{{$formName}}Message').html(json.ajaxSettings).show().removeClass('hide');
                    return false;
                }
            }
            $("#{{$formName}}").ajaxForm(options);
        }
    }
    $(document).ready(function() {
        {{$formName}}.ini();
    });
</script>
