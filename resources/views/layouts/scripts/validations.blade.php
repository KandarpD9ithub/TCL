<script type="text/javascript">
    $(document).ready(function(){
        jQuery.validator.addClassRules('category', {
            unique: true,
            required: true
        });
        jQuery.validator.addClassRules('products', {
            unique: true,
            required: true
        });
        jQuery.validator.addClassRules('price', {
            required: true,
            maxlength: 5,
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
        $('body').on('mouseover', '#create-product-price', function(){
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
                message : {
                    "product_price[][price]": {
                        required: '* Required'
                    },
                    "product_price[][product_id]": {
                        required: '* Required',
                        unique: "Product name already taken"
                    }
                }
            });
        });
    });

</script>