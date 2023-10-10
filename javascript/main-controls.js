/**
 * Pagination Controls 
 *
 * 
 */
var MainControls = new Object();

/**
 * Pagination Controls Initializer
 *
 * This is the primary funciton for this object. It will set 
 * the properties and call all other functions.
 */
MainControls.init = function() {
    MainControls.url = window.location.pathname.split('/');
    MainControls.view = MainControls.url[2];
    MainControls.ids = [];

    MainControls.deleteRecords();
    MainControls.userControls();
    MainControls.sitemapControls();
}

/**
 * Delete Records
 * 
 * This function will delete all the checked off items in the list.
 * Records are deleted by (id) so this function will work across 
 * the board for any list.
 */
MainControls.deleteRecords = function() {
    $('body').on('click', '.btn-mass-delete', function() {
        var ids = $('.checkbox').serializeArray();
        var btn = $(this);
        var btnhtml = btn.html();
        $.ajax({
            url: '/admin/' + MainControls.view + '/delete',
            type: 'POST',
            data: ids,
            success: function(response, status, xhr) {
                if ($.trim(response)) {
                    var data = JSON.parse(response);
                    $('.alert-area').append('<div class="alert ' + data.alert + ' space-bottom-15"><strong>' + data.alert + '!</strong> ' + data.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                    Pagination.drawTable();
                } 
            },
            complete: function() {
                btn.prop('disabled', false).html(btnhtml);
            }
        });
    });
}

MainControls.userControls = function() {
    // Promote user
    $('#user-group').on('change', function() {
        var ids = $('.checkbox').serializeArray();
        MainControls.editUser(ids, $(this).val());
    });
}

MainControls.sitemapControls = function() {
    // Save Sitemap
    $('body').on('click', '.btn-save', function() {
        var post = $('.sort-order').serialize();
        var btn = $(this);
        var btnhtml = btn.html();
        $.ajax({
            url: '/admin/sitemap/save',
            type: 'POST',
            data: post,
            success: function(response, status, xhr) {
                if ($.trim(response)) {
                    var data = JSON.parse(response);
                    $('.alert-area').html('<div class="alert ' + data.alert + ' space-bottom-15"><strong>' + data.alert + '!</strong> ' + data.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                    Pagination.drawTable();
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(btnhtml);
            }
        });
    });

    $('body').on('click', '.btn-hide', function() {
        var ids = $('.checkbox').serializeArray();
        var btn = $(this);
        var btnhtml = btn.html();
        $.ajax({
            url: '/admin/sitemap/hide/true',
            type: 'POST',
            data: ids,
            success: function(response, status, xhr) {
                if ($.trim(response)) {
                    var data = JSON.parse(response);
                    $('.alert-area').html('<div class="alert ' + data.alert + ' space-bottom-15"><strong>' + data.alert + '!</strong> ' + data.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                    Pagination.drawTable();
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(btnhtml);
            }
        });
    });

    $('body').on('click', '.btn-show', function() {
        var ids = $('.checkbox').serializeArray();
        var btn = $(this);
        var btnhtml = btn.html();
        $.ajax({
            url: '/admin/sitemap/hide/false',
            type: 'POST',
            data: ids,
            success: function(response, status, xhr) {
                if ($.trim(response)) {
                    var data = JSON.parse(response);
                    $('.alert-area').html('<div class="alert ' + data.alert + ' space-bottom-15"><strong>' + data.alert + '!</strong> ' + data.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                    Pagination.drawTable();
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(btnhtml);
            }
        });
    });
}

MainControls.editUser = function(ids, group) {
    $.ajax({
        url: '/admin/users/edit',
        type: 'POST',
        data: {ids: ids, group: group},
        success: function(response, status, xhr) {
            $('#group-none').prop('selected', true);
            if ($.trim(response)) {
                var data = JSON.parse(response);
                $('.alert-area').html('<div class="alert ' + data.alert + ' space-bottom-15"><strong>' + data.alert + '!</strong> ' + data.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                Pagination.drawTable();
            }
        },
        complete: function() {
            ids = [];
            $('.btn-list-control').prop('disabled', true);
        }
    });
}