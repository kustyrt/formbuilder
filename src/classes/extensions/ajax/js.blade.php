<script type="text/javascript">
    {{$formName}} = {
        send:false,
        ini:function(){
            {{$formName}}.validate();
            {{$formName}}.ajax();
        },
        validate:function(){
            /*$("#{{$formName}}").validetta({
                realTime     : true
            });*/
        },
        ajax:function(){
            var options = {
                'url': '/admin/geo/country/edit',

                @if( !empty($formAction) )
                'url': '{{$formAction}}',
                @endif
                'dataType': 'json',
                'type': 'post',
                'success': function (json) {

                if (json && json.error) {
                        $('#{{$formName}}Message').html(json.error);
                        $('#{{$formName}} [name=' + json.field + ']').focus();
                    } else if (json && json.url) {
                        window.location = json.url;
                    } else {
                        window.location.reload(true);
                    }
                },
                'error': function (event, jqXHR, ajaxSettings) {
                    $('#{{$formName}}Message').html(json.ajaxSettings);
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
