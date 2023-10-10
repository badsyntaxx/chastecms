
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
    Common.openMobileNav();
    Common.highlightNavLink();
    Common.goToTop();
    Common.search();
    Common.alertEvents();
    Common.buttonClick();
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
    var url = pieces[1];
    if (url == '') url = 'home';
    var nav_class = '.nav-link.' + url;
    $(nav_class).addClass('current');
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
        if ($(this).hasClass('.no-load') == false) {
            $(this).html('<i class="fas fa-spinner fa-fw fa-spin"></i> loading...');
        }   
    });
}

$(document).ready(function() {

    $('body').on('click', '.alert-continue', function() {
        window.location.reload();
    });
    $('body').on('click', '.alert-close', function() {
        $(this).parent().remove();
    });
    $('body').on('click', '.alert-redirect', function() {
        window.location.replace('/home');
    });

    $('input').keypress(function(e){
        if (e.which == 13) return false;
        if (e.which == 13) e.preventDefault();
    });

});