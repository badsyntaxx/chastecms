
/**
 * Common Javascript 
 *
 * Common js used site wide. Most of this will be used on every page because
 * a lot of it is for the header and navigation stuff. Dropdown menus, to 
 * top buttons, ect.
 */
var Common = new Object();

Common.btn = '';
Common.btnhtml = '';

Common.init = function() {
    Common.dropMenusDown();
    Common.highlightNavLink();
    Common.goBack();
    Common.getThemeSetting();
    Common.getMenuSetting();
    Common.changeThemeSetting();
    $('.main-nav').on('click', '.btn-menu', function(e) {
        e.preventDefault();
        Common.changeMenuSetting();
    });
    Common.alertEvents();
    Common.buttonClick();

    $('input').keypress(function(e){
        if (e.which == 13) return false;
        if (e.which == 13) e.preventDefault();
    });
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
    var url = pieces[2];
    
    if (url == '') url = 'overview';
    
    var nav_class = '.nav-link.' + url;
    $(nav_class).addClass('current');
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
    $.ajax({
        url: '/admin/settings/getThemeSetting',
        type: 'GET',
        success: function(response, status, xhr) {
            if ($.trim(response)) {
                if (response == 0) {
                    $('body').removeClass('dark-theme').addClass('light-theme');
                    $('.btn-theme i').remove();
                    $('.btn-theme').prepend('<i class="fas fa-sun fa-fw"></i>');
                }
                if (response == 1) {
                    $('body').removeClass('light-theme').addClass('dark-theme');
                    $('.btn-theme i').remove();
                    $('.btn-theme').prepend('<i class="fas fa-moon fa-fw"></i>');
                }
            }
        }
    });
}

Common.changeThemeSetting = function() {
    $('.account-nav').on('click', '.btn-theme', function() {
        $.ajax({
            url: '/admin/settings/changeAdminThemeSetting',
            type: 'GET',
            success: function(response, status, xhr) {
                if ($.trim(response)) {
                    if (response == 0) {
                        $('body').removeClass('dark-theme').addClass('light-theme');
                        $('.btn-theme i').remove();
                        $('.btn-theme').prepend('<i class="fas fa-sun fa-fw"></i>');
                    }
                    if (response == 1) {
                        $('body').removeClass('light-theme').addClass('dark-theme');
                        $('.btn-theme i').remove();
                        $('.btn-theme').prepend('<i class="fas fa-moon fa-fw"></i>');
                    }
                }
            }
        });
    });
}

Common.getMenuSetting = function() {
    $.ajax({
        url: '/admin/settings/getMenuSetting',
        type: 'GET',
        success: function(response, status, xhr) {
            if ($.trim(response)) {
                if (response == 0) {
                    if ($('.nav').hasClass('menu-open') == true) {
                        $('.nav').removeClass('menu-open');
                        $('.workarea').removeClass('menu-open');
                        $('.btn-menu i').removeClass('fa-toggle-on').addClass('fa-toggle-off');
                    }
                }
                if (response == 1) {
                    if ($('.nav').hasClass('menu-open') == false) {
                        $('.nav').addClass('menu-open');
                        $('.workarea').addClass('menu-open');
                        $('.btn-menu i').addClass('fa-toggle-on').removeClass('fa-toggle-off');
                    }
                }
            }
        }
    });
}

Common.changeMenuSetting = function() {
    
    $.ajax({
        url: '/admin/settings/changeMenuSetting',
        type: 'GET',
        success: function(response, status, xhr) {
            if ($.trim(response)) {
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
        }
    });
    $('.nav').toggleClass('menu-open');
    $('.btn-menu i').toggleClass('fa-toggle-on').toggleClass('fa-toggle-off');
    $('.workarea').toggleClass('menu-open');
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
            $(this).html('<i class="fas fa-sync fa-fw fa-spin"></i> loading...');
        }   
    });
}

Common.echo = function(string) {
    $('body').prepend(string);
}