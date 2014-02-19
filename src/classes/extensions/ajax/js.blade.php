<script>
    $(document).ready(function() {

        $("#{{$formName}}").validetta();

        /*
        var options = {
            'url': '{{$formAction}}',
            'dataType': 'json',
            'type': 'post',
            'success': function (json) {
                if (json && json.error) {
                    $('#{{$formName}}Message').html(json.error);
                    $('#{{$formName}} [name=' + json.field + ']').focus();
                } else if (json && json.url) {
                    window.location = json.url;
                    //window.location.reload(true);
                } else {
                    window.location.reload(true);
                }
            },
            'error': function (event, jqXHR, ajaxSettings) {
                //alert('Попробуйте еще раз!');
                return false;
            },
            beforeSubmit: function(arr, $form, options) {
               return false;
            }
        }
        $("#{{$formName}}").ajaxForm(options);*/
    });
</script>
