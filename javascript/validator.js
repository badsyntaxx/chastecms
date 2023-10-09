
/**
 * Validator
 *
 * The validator plugin validates user forms. It checks basic inputs like 
 * email and passwords. Using the options you can choose to check only
 * the inputs you want by setting the option to true.
 *
 * Example
 *     $('#contact-form').validator({
 *         firstnameValid : false,
 *         formButton : '#login-button'
 *     }); 
 */
(function($) {
    $.fn.validator = function(options) {

        var settings = $.extend({
            form : $(this),
            formButton : '.btn',
            checkUsername : true,
            checkUsernameTaken : true,
            checkFirstname : true,
            checkLastname : true,
            checkEmail : true,
            checkEmailTaken : true,
            checkPhone : true,
            checkFax : true,
            checkSubject : true,
            checkPassword : true,
            showPassword : true
        }, options);

        var action = {
            checkUsernameValidity: function(value) {
                if (settings.checkUsername == true) {
                    var username = $.trim(value.replace(/\s+/g, ''));
                    if (username.length > 0 && /^[a-zA-Z0-9-_]{1,20}$/.test(username) == false) {
                        $('.username .alert-inline').remove('div');
                        $('.username').append('<div class="alert-inline"><b>Usernames must:</b><br>- Be 20 characters or less.<br>- Only contain letters (a) to (z), numbers (1) to (9), underscores (_) or dashes (-).</div>');
                        $('#username').css({'border-color':'#ff6868'}).addClass('error');
                    } else {
                        $('.username .alert-inline').remove();
                        $('#username').css({'border-color':''}).removeClass('error');
                    }
                }
            },
            checkUsernameAvailability: function(value) {
                if (settings.checkUsernameTaken == true) {
                    var username = $.trim(value.toUpperCase().replace(/\s+/g, ''));
                    $.post('/account/checkUsername', $(settings.form).serialize()).done(function(response) {
                        if (username.length > 0 && username == response) {
                            $('.username .alert-inline').remove();
                            $('.username').append('<div class="alert-inline">Username is already taken.</div>');
                            $('#username').css({'border-color':'#ff6868'}).addClass('error');
                            action.checkRequiredFields(); // Necessary cuz ajax
                        }
                    });
                }
            },
            checkFirstnameValidity: function(firstname) {
                if (settings.checkFirstname == true) {
                    if (firstname.length > 0 && /^[a-zA-Z]{1,20}$/.test(firstname) == false) {
                        $('.firstname .alert-inline').remove();
                        $('.firstname').append('<div class="alert-inline">Names should be letters A-Z and no more than 20 characters.</div>');
                        $('#firstname').css({'border-color':'#ff6868'}).addClass('error');
                    } else {
                        $('.firstname .alert-inline').remove();
                        $('#firstname').css({'border-color':''}).removeClass('error');
                    }
                }
            },
            checkLastnameValidity: function(lastname) {
                if (settings.checkLastname == true) {
                    if (lastname.length > 0 && /^[a-zA-Z]{1,20}$/.test(lastname) == false) {
                        $('.lastname .alert-inline').remove();
                        $('.lastname').append('<div class="alert-inline">Names should be letters A-Z and no more than 20 characters.</div>');
                        $('#lastname').css({'border-color':'#ff6868'}).addClass('error');
                    } else {
                        $('.lastname .alert-inline').remove();
                        $('#lastname').css({'border-color':''}).removeClass('error');
                    }
                }
            },
            checkEmailValidity: function(email) {
                if (settings.checkEmail == true) {
                    if (email.length > 3 && /^[A-Z0-9._-]+@([A-Z0-9_-]+\.)+[A-Z]{2,4}$/i.test(email) == false) {
                        $('.email .alert-inline').remove();
                        $('.email').append('<div class="alert-inline">Email address is invalid.</div>')
                        $('#email').css({'border-color':'#ff6868'}).addClass('error');
                    } else {
                        $('.email .alert-inline').remove();
                        $('#email').css({'border-color':''}).removeClass('error');
                    }
                }
            },
            checkEmailAvailability: function(email) {
                if (settings.checkEmailTaken == true) {
                    $.post('/account/checkEmail', $(settings.form).serialize()).done(function(response) {   
                        if (email.length > 5 && email == response) {
                            $('.email .alert-inline').remove();
                            $('.email').append('<div class="alert-inline">Email taken.</div>');
                            $('#email').css({'border-color':'#ff6868'}).addClass('error');
                            action.checkRequiredFields(); // Necessary cuz ajax
                        }
                    });
                }
            },
            checkPhoneNumber: function(phonenumber) {
                if (settings.checkPhone == true) {
                    if (phonenumber.length > 0) {
                        phonenumber = phonenumber.replace(/[^0-9- ]/g, '');
                        $('#phone').val(phonenumber);
                    }
                }
            },
            checkFaxNumber: function(faxnumber) {
                if (settings.checkFax == true) {
                    if (faxnumber.length > 0) {
                        faxnumber = faxnumber.replace(/[^0-9- ]/g, '');
                        $('#fax').val(faxnumber);
                    }
                }
            },
            checkSubjectValidity: function(subject) {
                if (settings.checkSubject == true) {
                    if (subject.length > 0 && /^[a-zA-Z0-9,. ]{1,100}$/.test(subject) == false) {
                        $('.subject .alert-inline').remove();
                        $('.subject').append('<div class="alert-inline">Subject should only include characters a-z 0-9 and be 100 characters or less.</div>');
                        $('#subject').css({'border':'1px solid #ff6868'}).addClass('error');
                    } else {
                        $('.subject .alert-inline').remove();
                        $('#subject').css({'border-color':''}).removeClass('error');
                    }
                }
            },
            checkPasswordStrength: function(password) {
                if (settings.checkPassword == true) {
                    if (password.length > 0) {
                        if (/^(?=.*\d)(?=.*[a-z]).{8,}$/.test(password) == false) {
                            $('.password-strength').remove();
                            $('.password').append('<div class="password-strength"><i>WEAK</i></div>').css({'color':'#ff6868'});
                        }
                        if (/^(?=.*\d)(?=.*[a-z]).{8,}$/.test(password) == true) {
                            $('.password-strength').remove();
                            $('.password').append('<div class="password-strength"><i>MEDIUM</i></div>').css({'color':'rgb(237, 234, 3)'});
                        }
                        if (/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(password) == true) {
                            $('.password-strength').remove();
                            $('.password').append('<div class="password-strength"><i>STRONG</i></div>').css({'color':'rgb(90, 160, 85)'});
                        }
                        if (/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*=_+<>?]).{8,}$/.test(password) == true) {
                            $('.password-strength').remove();
                            $('.password').append('<div class="password-strength"><i>VERY STRONG</i></div>').css({'color':'rgb(90, 160, 85)'});
                        }
                    } else {
                        $('.password-strength').remove();
                    }
                }
            },
            checkPasswordMatch: function(password, confirm) {
                if (password && confirm) {
                    if (password != confirm) {
                        $('.confirm .alert-inline').remove();
                        $('.confirm').append('<div class="alert-inline">Passwords do not match.</div>');
                        $('#confirm').css({'border-color':'#ff6868'}).addClass('error');
                    } else {
                        $('.confirm .alert-inline').remove();
                        $('#confirm').css({'border-color':''}).removeClass('error');
                    }
                    action.checkRequiredFields();
                }
            },
            revealPassword: function() {
                if (settings.showPassword == true) {
                    var password = document.getElementById('password');
                    var confirm = document.getElementById('confirm');
                    if (!confirm) {
                        confirm = '';
                    }
                    $('.fa-eye').toggleClass('fa-eye-slash');
                    if (password.type == 'password') {
                        password.type = 'text';
                        confirm.type = 'text';
                    } else if (password.type == 'text') {
                        password.type = 'password';
                        confirm.type = 'password';
                    }
                }
            },
            checkRequiredFields: function() {
                var complete = true;
                var inputs = $(settings.form).find(':input'); // Should return all input elements in that specific form.

                $('[required]').each(function() {
                    if ($(this).val() == '') {
                        complete = false;
                    }
                });

                inputs.each(function() {
                    if ($(this).hasClass('error') == true) {
                        complete = false;
                    }
                });

                if (complete == true) {
                    $(settings.formButton).prop('disabled', false);
                } 
                if (complete == false) {
                    $(settings.formButton).prop('disabled', true);
                }
            }
        }

        return this.each(function() {
            $('body').on('keyup change', settings.form, function() {
                action.checkRequiredFields();
            });
            $(settings.form).on('keydown keyup change', '#username', function() {
                action.checkUsernameValidity($(this).val());
                action.checkUsernameAvailability($(this).val());
            });
            $(settings.form).on('keydown keyup change', '#firstname', function() {
                action.checkFirstnameValidity($(this).val());
            });
            $(settings.form).on('keydown keyup change', '#lastname', function() {
                action.checkLastnameValidity($(this).val());
            });
            $(settings.form).on('keydown keyup change', '#email', function() {
                action.checkEmailValidity($(this).val());
                action.checkEmailAvailability($(this).val());
            });
            $(settings.form).on('keydown keyup change', '#phone', function() {
                action.checkPhoneNumber($(this).val());
            });
            $(settings.form).on('keydown keyup change', '#fax', function() {
                action.checkFaxNumber($(this).val());
            });
            $(settings.form).on('keydown keyup change', '#subject', function() {
                action.checkSubjectValidity($(this).val());
            });
            $(settings.form).on('keydown keyup change', '#password', function() {
                action.checkPasswordStrength($(this).val());
            });
            $(settings.form).on('keydown keyup change', '#password, #confirm', function() {
                action.checkPasswordMatch($('#password').val(), $('#confirm').val());
            });
            $('.show-pw').on('mousedown', function() {
                action.revealPassword();
            });
        });
    }
}(jQuery));