
/**
 * Common Javascript 
 *
 * Common js used site wide. Most of this will be used on every page because
 * a lot of it is for the header and navigation stuff. Dropdown menus, to 
 * top buttons, ect.
 */
var Common = new Object();

$(window).on('load', function() {
    Object.keys(Common).forEach(function(key) {
        var func = Common[key];
        if (typeof func === 'function') {
            try {
                func();
            } catch (error) {
                $('body').prepend(error.stack + '\n');
                window.onerror = function(error, file, line, col) {
                    $('body').prepend(error + ' File: ' + file + ' Line: ' + line + ' Col: ' + col + '\n');
                    return false;
                };
            }
        }
    });
});

Common.adjustBodyHeight = function() {
    $('body').css({'min-height' : $(window).height()});
}

// Drop menus
Common.dropMenusDown = function() {
    $('.nav').on('click', '.dropdown-button .db', function() {
        return false;
    });  
    $('.nav ul').on('click', '.dropdown-button', function() {
        $('.dropdown-menu', this).toggleClass('active');
        $('.nav-toggle', this).toggleClass('fa-caret-up');
    });
}

// Hightlight nav link
Common.highlightNavLink = function() {
    var pieces = window.location.pathname.split('/');
    var url = pieces.length >= 3 ? pieces[2] : 'dashboard';
    var nav_class = '.nav-link.' + url;

    $(nav_class).addClass('current');
    $('.nav-link.current').parent().parent().addClass('active');
}

Common.showLoader = function(param) {
    $(param).prepend('<div class="loading"><div class="loading-spinner"><i class="fas fa-circle-notch fa-fw fa-spin"></i></div></div>');
}

Common.removeLoader = function() {
    $('.loading').remove();
}

Common.goBack = function() {
    $('body').on('click', '.btn-goback', function() { 
        window.history.back(); 
    });
}

Common.getThemeSetting = function() {
    $.get('/admin/settings/getThemeSetting', function(response) {
        if (isJson(response)) {
            if (response == 0) {
                $('.btn-theme i').remove();
                $('.btn-theme').prepend('<i class="fas fa-sun fa-fw"></i>');
            }
            if (response == 1) {
                $('.btn-theme i').remove();
                $('.btn-theme').prepend('<i class="fas fa-moon fa-fw"></i>');
            }
        }
    });
}

Common.changeThemeSetting = function() {
    $('.account-nav').on('click', '.btn-theme', function() {
        $.get('/admin/settings/changeThemeSetting', function(response) {
            if (isJson(response)) {
                if (response == 0) {
                    $('body').removeClass('dark-theme');
                    $('.btn-theme i').remove();
                    $('.btn-theme').prepend('<i class="fas fa-sun fa-fw"></i>');
                }
                if (response == 1) {
                    $('body').addClass('dark-theme');
                    $('.btn-theme i').remove();
                    $('.btn-theme').prepend('<i class="fas fa-moon fa-fw"></i>');
                }
            }
        });
    });
}

Common.getMenuSetting = function() {
    $.get('/admin/settings/getMenuSetting', function(response) {
        if (isJson(response)) {
            if (response == 0) {
                $('.btn-menu i').addClass('fa-toggle-off').removeClass('fa-toggle-on');
            }
            if (response == 1) {
                $('.btn-menu i').addClass('fa-toggle-on').removeClass('fa-toggle-off');
            }
        }
    });
}

Common.changeMenuSetting = function() {
    $('.main-nav').on('click', '.btn-menu', function() {
        $.get('/admin/settings/changeMenuSetting', function(response) {
            console.log(response);
            if (isJson(response)) {
                if (response == 0) {
                    $('.btn-menu i').removeClass('fa-toggle-on').addClass('fa-toggle-off');
                    $('.nav').removeClass('menu-open');
                    $('.workarea').removeClass('menu-open');
                }
                if (response == 1) {
                    $('.btn-menu i').addClass('fa-toggle-on').removeClass('fa-toggle-off');
                    $('.nav').addClass('menu-open');
                    $('.workarea').addClass('menu-open');
                }
            }
        });
        $('.nav').toggleClass('menu-open');
        $('.btn-menu i').toggleClass('fa-toggle-on').toggleClass('fa-toggle-off');
        $('.workarea').toggleClass('menu-open');
        return false;
    });
}

Common.alertEvents = function() {
    $('body').on('click', '.alert-continue', function() {
        location.reload();
    });
    $('body').on('click', '.alert-close', function() {
        $(this).parent().remove();
    });
    $('body').on('click', '.alert-redirect', function() {
        location.replace('/admin/home');
    });
}

Common.buttonClick = function() {
    $('body').on('click', '.btn', function() {
        Common.btn = $(this);
        Common.btnhtml = Common.btn.html();
        $('.alert').remove();
        $('.alert-area').html('');
        if ($(this).hasClass('no-load') == false) {
            $(this).html('<i class="fas fa-spinner fa-fw fa-spin"></i> loading...');
        }   
    });

    $(document).ajaxComplete(function(event, request, setting) {
        if (Common.btn) {
            Common.btn.prop('disabled', false).html(Common.btnhtml);
        }
    });
}

Common.liveSearch = function() {
    $('.search').on('keyup', '#search-string', function() {
        if ($('#search-string').val().length > 1) {
            $('.search-results').css({'display':'block'});
            Common.showLoader('.search-results');
            $.post('/admin/search/liveSearch', $('#search-string').serialize(), function(response) {
                if (isJson(response)) {
                    Common.removeLoader();
                    $('.search-results li').remove('');
                    var json = JSON.parse(response);
                    var users = json.users;
                    var cables = json.cables;
                    if (users.length) {
                        $('.search-results-users em').text('Users:');
                        $.each(users, function(index, item) {
                            $('.search-results-users').append('<li><a href="/users/user/' + item.users_id + '">' + item.firstname + ' ' + item.lastname + ' (' + item.email + ')</a></li>');
                        });
                    } else {
                        $('.search-results-users li').remove('');
                        $('.search-results-users em').text('');
                    }
                    if (cables.length) {
                        $('.search-results-cables em').text('cables:');
                        $.each(cables, function(index, item) {
                            $('.search-results-cables').append('<li><a href="/cables/cable/' + item.cable_num + '">' + item.cable_num + ' (' + item.tower + ') (' + item.spec + ') (' + item.tenant + ')</a></li>');
                        });
                    } else {
                        $('.search-results-cables li').remove('');
                        $('.search-results-cables em').text('');
                    }
                }
            });
        } else {
            $('.search-results').css({'display':'none'});
        }
    });
}

function isJson(str) {
    if ($.trim(str)) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
}