<script>
    $(document).ready(function() {
        var options = {
            'url': '{{$formAction}}',
            'dataType': 'json',
            'type': 'post',
            'success': function (json) {
                if (json && json.error) {
                    $('#{{$formName}}Message').html(json.error).show();
                    $('form.{{$formName}} [name=' + json.field + ']').focus();
                } else if (json && json.url) {
                    window.location = json.url;
                    //window.location.reload(true);
                } else if (json && json.msg) {
                    $('#{{$formName}}Message').html(json.msg).show();
                    $('#{{$formName}}').hide();

                }else {
                    window.location.reload(true);
                }
            },
            'error': function (event, jqXHR, ajaxSettings) {
                //alert('Попробуйте еще раз!');
                return false;
            }
        }
        $("#{{$formName}}").ajaxForm(options);
    });
</script>
