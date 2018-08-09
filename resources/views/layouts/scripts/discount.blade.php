<script type="text/javascript">
    $(document).ready(function(){
        $('#rule_on').on('change', function() {
            var ruleOn = $(this).val();
            if (ruleOn == 'products') {
                $('.products').removeClass('hide');
                $('.categories').addClass('hide');
                $('.categories select').multipleSelect('uncheckAll');
            } else if (ruleOn == 'categories') {
                $('.products').addClass('hide');
                $('.categories').removeClass('hide');
                $('.products select').multipleSelect('uncheckAll');
            } else {
                $('.products').addClass('hide');
                $('.categories').addClass('hide');
            }
        });
        $('.rule-on select').multipleSelect({
            selectAllText: 'All',
            width: '100%',
            filter: true
        });

        $('body').on('mouseover', '.from_date' , function(){
            $(this).datetimepicker({
                showButtonPanel: false,
                dateFormat: "dd-M-y",
                changeMonth: true,
                changeYear: true,
                showMonthAfterYear: true,
                constrainInput: false,
                minDate:0,
                setDate: 'today'
            })
        }).on('mouseover', '.to_date' , function(){
            $(this).datepicker({
                showButtonPanel: false,
                dateFormat: "dd-M-y",
                changeMonth: true,
                changeYear: true,
                showMonthAfterYear: true,
                constrainInput: false,
                minDate:0,
                setDate: 'today'
            })
        });
        $('#rule_type').on('change', function() {
            var ruleType = $(this).val();
            if (ruleType == 'offer') {
                $('.offer').removeClass('hide');
                $('.discount').addClass('hide');
                $(".discount").attr("disabled", true);
                $('.offer-amount').removeClass('hide');
                $('.amount-qty').removeClass('hide');
                $('.amount').addClass('hide');
            } else {
                $('.offer').addClass('hide');
                $(".offer").attr("disabled", true);
                $('.discount').removeClass('hide');
                $('.offer-amount').addClass('hide');
                $('.amount-qty').addClass('hide');
                $('.amount').removeClass('hide');
            }
        });

    });

</script>