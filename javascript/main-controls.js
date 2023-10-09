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
        $.post('/admin/' + MainControls.view + '/delete', ids, function(response) {
            if (isJson(response)) {
                var json = JSON.parse(response);
                $('.alert-area').append('<div class="alert ' + json.alert + '"><strong>' + json.alert + '!</strong> ' + json.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                Pagination.refreshTable();
            }
        });
    });
}

MainControls.userControls = function() {
    $('#user-group').on('change', function() {
        var ids = $('.checkbox').serializeArray();
        MainControls.editUser(ids, $(this).val());
    });
}

MainControls.editUser = function(ids, group) {
    var data = {ids: ids, group: group};
    $.post('/admin/users/edit', data, function(response) {
        $('.alert').remove();
        $('#group-none').prop('selected', true);
        if (isJson(response)) {
            var json = JSON.parse(response);
            $('.alert-area').append('<div class="alert ' + json.alert + '"><strong>' + json.alert + '!</strong> ' + json.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
            Pagination.refreshTable();
        }
    }).always(function() {
        ids = [];
        $('.btn-list-control').prop('disabled', true);
    });
}

MainControls.sitemapControls = function() {
    $('body').on('click', '.btn-save', function() {
        var post = $('.sort-order').serialize();
        $.post('/admin/sitemap/save', post, function(response) {  
            if (isJson(response)) {
                var json = JSON.parse(response);
                $('.alert-area').html('<div class="alert ' + json.alert + ' space-bottom-15"><strong>' + json.alert + '!</strong> ' + json.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                Pagination.refreshTable();
            } 
        });
    });

    $('body').on('click', '.btn-hide', function() {
        var ids = $('.checkbox').serializeArray();
        $.post('/admin/sitemap/hide/true', ids, function(response) {  
            if (isJson(response)) {
                var json = JSON.parse(response);
                $('.alert-area').html('<div class="alert ' + json.alert + ' space-bottom-15"><strong>' + json.alert + '!</strong> ' + json.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                Pagination.refreshTable();
            }
        });
    });

    $('body').on('click', '.btn-show', function() {
        var ids = $('.checkbox').serializeArray();
        $.post('/admin/sitemap/hide/false', ids, function(response) {  
            if (isJson(response)) {
                var json = JSON.parse(response);
                $('.alert-area').html('<div class="alert ' + json.alert + ' space-bottom-15"><strong>' + json.alert + '!</strong> ' + json.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                Pagination.refreshTable();
            }
        });
    });

    $('body').on('click', '.btn-refresh', function() {
        $.get('/admin/sitemap/generate', function(response) {       
            if (isJson(response)) {
                var json = JSON.parse(response);
                $('.alert-area').html('<div class="alert ' + json.alert + ' space-bottom-15"><strong>' + json.alert + '!</strong> ' + json.message + ' <button type="button" class="alert-close"><i class="fas fa-times fa-fw"></i></button>');
                Pagination.refreshTable();
            }
        });
    });
}