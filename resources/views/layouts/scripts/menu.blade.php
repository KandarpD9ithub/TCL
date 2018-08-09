<script type="text/javascript">
    $(document).ready(function(){
        $('.select-product select').multipleSelect({
            selectAllText: 'All',
            width: '100%',
            filter: true,
            selectAll: false,
            placeholder: "Choose..."
        });
        $('.select-category select').multipleSelect({
            single: true,
            selectAllText: 'All',
            width: '100%',
            filter: true,
            placeholder: "Choose..."
        });
        $('body').on('mouseleave', '.product-name', function(){
            $(this).find('.delete-icon').toggleClass('hide');
        });
        $('body').on('mouseenter', '.product-name', function(){
            $(this).find('.delete-icon').toggleClass('hide');
        });

        jQuery.validator.addClassRules('product', {
            unique: true,
            required: true
        });
        jQuery.validator.addClassRules('category', {
            unique: true,
            required: true
        });
        jQuery.validator.addMethod("unique", function(value, element, params) {
            var prefix = params;
            var selector = jQuery.validator.format("[name!='{0}'][name^='{1}'][unique='{1}']", element.name, prefix);
            var matches = new Array();
            $(selector).each(function(index, item) {
                if (value == $(item).val()) {
                    matches.push(item);
                }
            });
            return matches.length == 0;
        }, "unique");
        jQuery.validator.classRuleSettings.unique = {
            unique: true
        };
        $('body').on('mouseover', '.candidateForm', function(){
            $(this).validate({
                ignore : ":hidden:not(.chosen, .from-to-range)",
                errorPlacement: function(error, element) {
                    if (element.parent().find('.formElementHelpText').length > 0) {
                        error.insertAfter(element.next());
                    } else if (element.hasClass('auto-complete')) {
                        error.insertAfter($('#'+element.attr('rel')));
                    } else {
                        error.insertAfter(element);
                    }
                },
                rules : {
                    attach_file: {
                        filesize :true,
                        extension: "{{{ Lang::get('common.jpg_jpeg_png') }}}",
                        required: true
                    }
                }
            });
        });
    });

</script>