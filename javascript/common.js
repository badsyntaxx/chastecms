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
    var header_height = $('header').outerHeight();
    var footer_height = $('footer').outerHeight();
    var remove_height = header_height + footer_height;
    var main_height = $(window).height() - remove_height;

    $('main').css({'min-height' : main_height});
}

// Drop menus
Common.dropMenusDown = function() {
    $('.nav ul').on('mouseover', '.dropdown-button', function() {
        if ($(window).width() > 768) {
            $('.dropdown-menu', this).addClass('active');
            $('i', this).removeClass('fa-caret-down').addClass('fa-caret-up');
        }
    });
    $('.nav ul').on('mouseout', '.dropdown-button', function() {
        if ($(window).width() > 768) {
            $('.dropdown-menu', this).removeClass('active');
            $('i', this).removeClass('fa-caret-up').addClass('fa-caret-down');
        }
    });  
    $('.nav ul').on('click', '.dropdown-button', function() {
        if ($(window).width() <= 768) {
            $('.dropdown-menu', this).toggleClass('active');
            $('i', this).toggleClass('fa-caret-up');
        }
    });
}

// Mobile navigation
Common.openMobileNav = function() {
    $('.header').on('click', '.menu-button', function() {
        $('.nav').toggleClass('active');
    });   
}

// Hightlight nav link
Common.highlightNavLink = function() {
    var pieces = window.location.pathname.split('/');
    var url = pieces.length >= 2 ? pieces[1] : 'home';
    var nav_class = '.nav-link.' + url;
    if (url) $(nav_class).addClass('current');
}

// Go to top button
Common.goToTop = function() {
    $('.main').append('<div class="to-top"><i class="fas fa-angle-double-up fa-fw"></i></div>');
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.to-top').addClass('visible');
        } else {
            $('.to-top').removeClass('visible');
        }
    });
    $('.to-top').click(function() {
        $('html, body').animate( {
            scrollTop: 0
        }, 300);
    });
}

Common.search = function() {
    $('.search button').click(function() {
        window.location.replace('/search/' + $('#search-term').val());
    });
    $('.search').keypress(function(e) {
        if (e.which == 13) { //Enter key pressed
            $('.search button').click(); //Trigger search button click event
            return false;
        }
    });
}

Common.showLoader = function(param) {
    $(param).prepend('<div class="loading"><div class="loading-spinner"><i class="fas fa-circle-notch fa-fw fa-spin"></i></div></div>');
}

Common.removeLoader = function() {
    $('.loading').remove();
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