$(document).ready(function() {
    var removedFieldSetCount = 0;
    var selectedCategory = [];
    var selectedProducts =[];
    $('body').on('click', '.addCollectionElement', function() {
        var parent = $(this).parents('.addMoreCollectionElementContainer');

        var currentCount = parent.find('.copy').length;

        var template = parent.find('.template').data('template');

        var indexPosition = currentCount + removedFieldSetCount;

        var remove = $('.copy:last select.category').val();
        selectedCategory.push(remove);

        template = template.replace(/_index_/g, indexPosition);

        template = $(template);
        for (var i=0; i<selectedCategory.length; i++) {
            template.find('select:first option[value="'+selectedCategory[i]+'"]').remove();
        }
        parent.find('.copy:last').after(template);
        parent.find('.copy:last').append('<div class="removeFieldSet col-md-1"><a href="javascript:void(0);" class="colorRed"><span class="glyphicon glyphicon-minus-sign"></span></a></span>');
        if(parent.find('.product')) {
            var product = $("#productCount").val();
            if(product == (currentCount+1)) {
                $('.addMore').addClass('hide')
            }
        }
        if(parent.find('.copy:last')) {
            $('.select-product select').multipleSelect({
                selectAllText: 'All',
                width: '100%',
                filter: true,
                placeholder: "Choose..."
            });
            $('.select-category select').multipleSelect({
                single: true,
                selectAllText: 'All',
                width: '100%',
                filter: true,
                placeholder: "Choose..."
            });
        }

    }).on('click', '.removeFieldSet a', function() {
        var parent = $(this).parents('.addMoreFormContainer');

        removedFieldSetCount++;

        $(this).parent().parent().remove();

        if (parent.find('.copy').length > 1 && parent.find('.copy:last').find('.removeFieldSet').length == 0) {
            parent.find('.copy:last').append('<div class="removeFieldSet col-md-1"><a href="javascript:void(0);" class="colorRed"><span class="glyphicon glyphicon-minus-sign"></span></a></span>');
        }
        if (parent.find('.copy').length == 0) {
            $(parent).addClass('hide');
        }
        if(parent.find('.product')) {
                $('.addMore').removeClass('hide')

        }

    }).on('keypress', 'input.keyFloat',function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;

        if (charCode == 46 && $(this).val().indexOf('.') != -1) {
            return false;
        }
        return (charCode > 47 && charCode < 58) // numbers(0-9) keys
            || charCode == 8 || charCode == 9 || charCode == 46
            || ((charCode == 37 || charCode == 39) && !evt.shiftKey && !evt.ctrlKey && !evt.altKey);

    }).on('keypress', 'input.keyNumSingle',function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        return (charCode > 47 && charCode < 58) // numbers(0-9) keys
        || charCode == 8 || charCode == 9
        || ((charCode == 37 || charCode == 39) && !evt.shiftKey && !evt.ctrlKey && !evt.altKey);

})
    var dateFormat = "dd-M-y",
        from = $( "#from" )
            .datetimepicker({
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                showMonthAfterYear: true,
                dateFormat: "dd-M-y",
                numberOfMonths: 1
            })
            /*.on( "change", function() {
                to.datetimepicker( "option", "minDate", getDate( this ) );
            })*/,
        to = $( "#to" ).datetimepicker({
            defaultDate: "+1w",
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd-M-y",
            showMonthAfterYear: true,
            numberOfMonths: 1
        })
           /* .on( "change", function() {
                from.datetimepicker( "option", "maxDate", getDate( this ) );
            })*/;

    function getDate( element ) {
        var date;
        try {
            date = $.datetimepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
            date = null;
        }

        return date;
    }


    $(function(){
        $('#save_value').click(function(){
            var val = [];
            $(':checkbox:checked').each(function(i){
                val[i] = $(this).val();
            });
        });
    });
    $(document).ready(function(){
        $('.dropdown-submenu a.test').on("click", function(e){
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
    });

    $('.other').click(function () {
        var selected = $('.other option:selected').html();

        if(selected == 'Other')
        {
            $('#tax').show();
        }
        else
        {
            $('#tax').hide();
        }

    });

    $('#tax').hide();
});





