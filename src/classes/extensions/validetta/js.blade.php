<script type="text/javascript">



    $(document).ready(function() {
        $("#{{$formName}}").validetta({
            customReg : {
                select : {
                    method :  /^[^0]$/,
                    errorMessage : 'Пожалуйста выберите'
                }
            },
            realTime     : true,
            conditional : {
                visible : function() {
                    if ( !$(this).is(':visible') ){
                        return true;
                    }
                    if ( $(this).is(':visible') &&  $(this).val()!='' ){
                        return true;
                    }
                    return false;
                },
                select_city : function() {
                    if ( $('#filing_city_id').val()=='' ){
                        return false;
                    }
                    return true;
                },
                select_value : function() {
                    if ( $(this).val()=='' ){
                        return false;
                    }
                    return true;
                }

            }
        });
    });
</script>
