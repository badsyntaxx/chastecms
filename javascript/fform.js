
/**
 * Fancy Form
 *
 * This script will make the label element look like a form placeholder.
 * It will also animate form input fields to resize them and move the 
 * labels out of the input fields.
 */
$(document).ready(function() {
    // Apparently some browsers view the password field as blank on load even if the user 
    // tells their browser to remember password and the view shows characters in the 
    // password field. This prevents the check input part of this script from working.
    // Selecting on the password element after 300 milliseconds is a workaround.
    setTimeout(function() {
        if ($('input').val() != '') {
            $('#password').focus();
        }
    }, 300);

    checkFields();

    $('.fancy-form').on('keyup change', function() {
        checkFields();
    });

    // If a label is click focus on its corresponding input field.
    $('.fancy-form').on('click', 'label', function() {
        $(this).next('input').focus();
        $(this).next('textarea').focus();
    });

    // When an input in a fancy row is focused on, change the input size and change the labels font size.
    $('.fancy-form').on('focus', '.fancy-row', function() {
        $('input', this).css({'height' : '45px', 'padding-top' : '18px'});
        $('textarea', this).css({'padding-top' : '25px'});
        $('label', this).css({'font-size' : '10px', 'display': 'block', 'color' : '#777'});
    });

    // When an input in a fancy row is no longer focused on, change the input size and change the labels font size.
    $('.fancy-form').on('blur', '.fancy-row', function() {
        if (!$('input, textarea', this).val()) {
            $('input', this).removeAttr('style');
            $('label', this).css({'font-size' : '14px', 'color' : '#bbb'});
        }
    });
});

function checkFields() {
    // Check each label and change its font size if its corresponding input is not blank.
    $('.fancy-form label').each(function() {
        if ($(this).next().val() != '') {
            $(this).css({'font-size': '10px'});
        }
    });  

    // Check each input and change its size if its value is not blank.
    $('.fancy-form input').each(function() {
        if ($(this).val() != '') {
            $(this).css({'height' : '45px', 'padding-top' : '18px'});
        }
    });

    // Check each textarea and change its padding if its value is not blank.
    $('.fancy-form textarea').each(function() {
        if ($(this).val() != '') {
            $(this).css({'padding-top' : '25px'});
        }
    });  
}