/**
 * Pagination
 */
var Pagination = new Object();

/**
 * Pagination Initializer
 *
 * This is the primary funciton for this object. It will set the properties and call all other functions.
 */
Pagination.init = function(view = null) {
    Pagination.view = view;
    Pagination.getPaginationParams();
    Pagination.drawTable();
    Pagination.goToPage();
    Pagination.goPrev();
    Pagination.goNext();
    Pagination.limitPageRecords();
    Pagination.sortRecords();
    Pagination.checkAll();
    Pagination.enableControls();
    Pagination.highlightRow();
}

Pagination.getPaginationParams = function() {
    $.ajax({
        url: '/admin/' + Pagination.view + '/setPaginationParams',
        type: 'GET',
        async: false, 
        success: function(response, status, xhr) {
            if ($.trim(response)) {
                var data = JSON.parse(response);
                Pagination.params = {orderby: data.orderby, direction: data.direction, page: data.page, limit: data.limit};
            } 
        }
    });
    return Pagination.params;
}

/**
 * Draw Table
 * 
 * This function is responsible for drawing a table of records in the list view. This function will also 
 * call drawPagination() and getTotalRecords().
 */
Pagination.drawTable = function() {
    $.ajax({
        url: '/admin/' + Pagination.view + '/drawTable',
        type: 'POST',
        data: Pagination.params,
        beforeSend: function() {
            $('.loading').show();
        },
        success: function(response, status, xhr) {
            // $('body').prepend(response);
            if ($.trim(response)) {
                var data = JSON.parse(response);
                // Draw the table
                $('.panel-content').html(data.table);
                // Number each row
                $('.data-row').each(function(index, item) {
                    var start_num = data.start;
                    var num = parseInt(index) + parseInt(start_num);
                    num++;
                    $(this).find('.number-col').text(num);               
                });

                Pagination.drawPagination();
                Pagination.getTotalRecords();
            } 
        },
        complete: function() {
            $('.loading').fadeOut(500);
        }
    });
}

/**
 * Draw Pagination
 * 
 * This function will create pagination links for the table list.
 */
Pagination.drawPagination = function() {
    $('.pagination').html('');
    $.ajax({
        url: '/admin/pagination/getPageTotal/' + Pagination.view,
        type: 'POST',
        data: Pagination.params,
        success: function(response, status, xhr) {
            if ($.trim(response)) {
                // Add previous button if page is not first page.
                if (Pagination.params.page != 1) {
                    $('.pagination').prepend('<button type="button" class="btn-prev"><i class="fas fa-caret-left fa-fw"></i></button><hr class="divider">');
                }
                // Add page links with page numbers.
                for (var i = 1; i <= response; i++) {
                    $('.pagination').append('<button type="button" class="btn-page">' + i + '</button><hr class="divider">');
                }
                // Add next button if page is not last page.
                if (Pagination.params.page != response) {
                    $('.pagination').append('<button class="btn-next"><i class="fas fa-caret-right fa-fw"></i></button>');
                }
                // Highlight current page in pagination nav.
                $('.pagination button').each(function() {
                    if ($(this).text() == Pagination.params.page) {
                        $(this).css({'box-shadow' : 'inset 0px 0px 3px 1px rgba(0, 0, 0, 0.1)', 'color' : '#aaa'});
                    }
                });
            } 
        }
    });
}

/**
 * Get Total Records
 * 
 * This function will get the total number of records and display the total at the bottom of the list.
 */
Pagination.getTotalRecords = function() {
    $.ajax({
        url: '/admin/pagination/getTotalRecordsNumber/' + Pagination.view,
        type: 'POST',
        data: Pagination.params,
        success: function(response, status, xhr) {
            if ($.trim(response)) {
                $('.total').text('Total: ' + response);
            } 
        }
    });
}

/**
 * Go to Page
 *
 * This function will re-draw the table starting at the page that is selected in the pagination. 
 */
Pagination.goToPage = function() {
    $('body').on('click', '.btn-page', function() {
        Pagination.params.page = $(this).text();
        Pagination.drawTable();
    });
}

/**
 * Go Previous Page
 * 
 * This function will re-draw the table one page down from the current page.
 */
Pagination.goPrev = function() {
    $('body').on('click', '.btn-prev', function() {
        Pagination.params.page = --Pagination.params.page;
        Pagination.drawTable();
    });
}

/**
 * Go Next Page
 * 
 * This function will re-draw the table one page up from the current page.
 */
Pagination.goNext = function() {
    $('body').on('click', '.btn-next', function() {
        Pagination.params.page = ++Pagination.params.page;
        Pagination.drawTable();
    });
}

/**
 * Limit Page Records
 * 
 * This function will limit the amount of records that are displayed in the table list.
 */
Pagination.limitPageRecords = function() {
    // Add selected prop to current page limit in select menu
    $('.limit' + Pagination.params.limit).prop('selected', true);

    // Choose max records to show per page.
    $('.limit-per-page').on('change', function() {
        $('.limit-per-page option').each(function() {
            if ($(this).prop('selected') == true) {
                Pagination.params.limit = $(this).text();
                Pagination.params.page = 1;
                Pagination.drawTable();
            }
        });
    });
}

/**
 * Sort Records
 * 
 * This function sorts the table when the user clicks a link in the table header.
 */
Pagination.sortRecords = function() {
    $('body').on('click', '.btn-sort', function() {
        // Set the order by the table header text.
        Pagination.params.orderby = $(this).text().toLowerCase().replace(/ /g, '_');

        // Determine the direction the table should sort by.
        if (Pagination.params.direction === 'asc') {
            Pagination.params.direction = 'desc';
        } else {
            Pagination.params.direction = 'asc';
        }
        Pagination.drawTable();
    });
}

/**
 * Check All
 * 
 * Check all checkboxes if the #check-all checkbox is checked.
 */
Pagination.checkAll = function() {
    $('body').on('click', '#check-all', function() {
        if ($('#check-all').prop('checked') == true) {
            $('.checkbox').prop('checked', true).change();
        }
        if ($('#check-all').prop('checked') == false) {
            $('.checkbox').prop('checked', false).change();
        }
    }); 
}

/**
 * Enable List Controls
 * 
 * This function will enable the master list controls. These buttons will be disabled by default but visible in 
 * the list view. This function listen for a checkbox to change. When it does it will  enable the list controls. 
 * These controls are different from the pagination and limit controls.
 */
Pagination.enableControls = function() {
    $('body').on('change', '.checkbox', function() {
        if ($('input:checkbox:checked').length) {
            $('.btn-list-control').prop('disabled', false);
        }
        if (!$('input:checkbox:checked').length) {
            $('.btn-list-control').prop('disabled', true);
        }
    });  
}

/**
 * Highlight Row
 * 
 * Whenever a row and an element inside a row is clicked this function will highlight that row by changing its 
 * background color.
 */
Pagination.highlightRow = function() {
    $('body').on('click', '.data-row', function() {
        $('tr').removeAttr('style');
        $('.data-row').removeClass('tr-highlight');
        $(this).addClass('tr-highlight');
    });
}