<script>
    var options = {
        @if ( false!==$formAction )
        'action': '/contacts',
        @endif
        'dataType': 'json',
        'type': 'post',
        'success': function (json) {
            if (json && json.error) {
                $('#contactFormMessage').html(json.error);
                $('form.contactForm [name=' + json.field + ']').focus();
            }
            else {
                var msg = json.success || "Спасибо!";
                $('form.contactForm').parent().append("<p class=\"thank\">" + msg + "</p>");
                $('form.contactForm').remove();
            }
        },
        'error': function (event, jqXHR, ajaxSettings) {
            //alert('Попробуйте еще раз!');
            return false;
        }
    }
    $("#{{$formName}}").ajaxForm(options);

</script>