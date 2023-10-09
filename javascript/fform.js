
(function($) {
    $.fn.fancyForm = function(options) {

        var settings = $.extend({
            form : '#' + $(this).attr('id'),
            row : '.fancy-row',
            height : 25,
            padding : 18,
            textarea_padding : 20,
            label_size : 10,
            label_color : '#ddd',
            speed : 300,
            ease : 'linear'
        }, options);

        var font_size = $('label').css('font-size');
        var input_height = $('input').css('line-height');
        var color = $('label').css('color');

        var action = {
            checkFields:function() {
                // Check each label and change its font size if its corresponding input is not blank.
                $(settings.form + ' label').each(function() {
                    if ($(this).next().val() != '') {
                        $(this).animate({'font-size': settings.label_size + 'px'}, settings.speed, settings.ease);
                    }
                });  
            
                // Check each input and change its size if its value is not blank.
                $(settings.form + ' input').each(function() {
                    if ($(this).val() != '') {
                        $(this).animate({'line-height' : settings.height + 'px', 'padding-top' : settings.padding + 'px'}, settings.speed, settings.ease);
                    }
                });
            
                // Check each textarea and change its padding if its value is not blank.
                $(settings.form + ' textarea').each(function() {
                    if ($(this).val() != '') {
                        $(this).animate({'padding-top' : settings.textarea_padding + 'px'}, settings.speed, settings.ease);
                    }
                });  
            },
            blurPassword: function() {
                // Apparently some browsers view the password field as blank on load even if the user 
                // tells the browser to remember password and the view shows characters in the 
                // password field. This prevents the check input part of this script from working.
                // Selecting on the password element after 300 milliseconds is a workaround.
                setTimeout(function() {
                    if ($('#password').val() != '') {
                        $('#password').focus().blur();
                    }
                }, 300);
            }
        }

        return this.each(function() {      
            action.checkFields();
            action.blurPassword();

            $(settings.form).on('keyup change', function() {
                action.checkFields();
            });

            // If a label is clicked, focus on its corresponding input field.
            $(settings.form).on('click', 'label', function() {
                $(this).next('input').focus();
                $(this).next('textarea').focus();
            });

            // When an input in a fancy row is focused on, change the input size and change the labels font size.
            $(settings.form).on('focus', settings.row, function() {
                $('input', this).animate({'line-height' : settings.height + 'px', 'padding-top' : settings.padding + 'px'}, settings.speed, settings.ease);
                $('textarea', this).animate({'padding-top' : settings.textarea_padding}, settings.speed, settings.ease);
                $('label', this).animate({'font-size' : settings.label_size + 'px', 'display': 'block', 'color' : settings.label_color}, settings.speed, settings.ease);
            });

            // When an input in a fancy row is no longer focused on, change the input size and change the labels font size.
            $(settings.form).on('blur', settings.row, function() {
                if (!$('input, textarea', this).val()) {
                    $('input', this).animate({'line-height': input_height, 'padding-top' : 0}, settings.speed, settings.ease);
                    $('label', this).animate({'font-size': font_size, 'color' : color}, settings.speed, settings.ease);
                }
            });
        });
    }
}(jQuery));