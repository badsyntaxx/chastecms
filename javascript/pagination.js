/**
 * Pagination
 */
var Pagination = new Object();

/**
 * Pagination Initializer
 *
 * This is the primary funciton for this object. It will set the properties and call all other functions.
 */
Pagination.init = function(arr) {
    Pagination.params = Pagination.makeParamObject(arr);

    Pagination.drawPagination();
    Pagination.drawTotalRecords();
    Pagination.numberRows(Pagination.params.start);
    Pagination.goToPage();
    Pagination.goPrev();
    Pagination.goNext();
    Pagination.limitPageRecords();
    Pagination.filterBy();
    Pagination.filter();
    Pagination.sortRecords();
    Pagination.checkAll();
    Pagination.enableControls();
    Pagination.highlightRow();
}

/**
 * Make Parameter Object
 * 
 * Create a data object with all the pagination parameters. Includes the starting page, total records ect.
 */
Pagination.makeParamObject = function(arr) {
    var data = {};
    $(arr).each(function(index, obj){
        if (obj.value !== '') data[obj.name] = obj.value;
    });
    return data;
}

/**
 * Refresh Table
 * 
 * This function is responsible for drawing a table of records in the list view. This function will also 
 * call drawPagination() and drawTotalRecords() and numberRows().
 */
Pagination.refreshTable = function() {
    $.post('/admin/' + Pagination.params.table + '/getTable', Pagination.params, function(response) {
        if (isJson(response)) {
            var json = JSON.parse(response);
            Pagination.params.total_pages = json.total_pages;
            Pagination.params.total_records = json.total_records;

            $('.panel-content').html(json.list);

            Pagination.numberRows(json.start);
            Pagination.drawPagination();
            Pagination.drawTotalRecords();
        }
    });
},

Pagination.numberRows = function(start) {
    $('.data-row').each(function(index, item) {
        var start_num = start;
        var num = parseInt(index) + parseInt(start_num);
        num++;
        $(this).find('.number-col').text(num);               
    });
},

/**
 * Draw Pagination
 * 
 * This function will create pagination links for the table list.
 */
Pagination.drawPagination = function() {
    // $('.pagination button').remove();
    $('#pagination-select option').remove();

    // Add previous button if page is not first page.
    if (Pagination.params.page == 1) {
        $('.pagination .btn-prev').attr('disabled', true);
    } else {
        $('.pagination .btn-prev').attr('disabled', false);
    }
    // Add page links with page numbers.
    for (var i = 1; i <= Pagination.params.total_pages; i++) {
        $('#pagination-select').append('<option value="' + i + '">' + i + '</option>');
    }
    // Add next button if page is not last page.
    if (Pagination.params.page == Pagination.params.total_pages) {
        $('.pagination .btn-next').attr('disabled', true);
    } else {
        $('.pagination .btn-next').attr('disabled', false);
    }
    // Highlight current page in pagination nav.
    $('#pagination-select option').each(function() {
        if ($(this).val() == Pagination.params.page) {
            $('#pagination-select').val(Pagination.params.page);
        }
    });
}

/**
 * Get Total Records
 * 
 * This function will get the total number of records and display the total at the bottom of the list.
 */
Pagination.drawTotalRecords = function() {
    $('.total').text('Total: ' + Pagination.params.total_records);
}

/**
 * Go to Page
 *
 * This function will re-draw the table starting at the page that is selected in the pagination. 
 */
Pagination.goToPage = function() {
    $('#pagination-select').on('change', function() {
        Pagination.params.page = $(this).val();
        Pagination.refreshTable();
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
        Pagination.refreshTable();
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
        Pagination.refreshTable();
    });
}

/**
 * Limit Page Records
 * 
 * This function will limit the amount of records that are displayed in the table list.
 */
Pagination.limitPageRecords = function() {
    // Add selected prop to current page limit in select menu
    $('.limit' + Pagination.params.record_limit).prop('selected', true);

    // Choose max records to show per page.
    $('.limit-per-page').on('change', function() {
        $('.limit-per-page option').each(function() {
            if ($(this).prop('selected') == true) {
                Pagination.params.page = 1;
                Pagination.params.record_limit = $(this).text();
                Pagination.refreshTable();
            }
        });
    });
}

/**
 * Filter Page Records
 * 
 * This function will filter what records are displayed in the table list.
 */
Pagination.filterBy = function() {
    var column = '';    

    $('.filter-by').on('change', function() {
        $('.filter-dropdown').css({'display' : 'none'});

        if ($('option:selected', this).val()) {
            column = $('option:selected', this).val();

            Pagination.params.column = column;

            $('#' + column).css({'display' : 'block'});
        } else {
            $('.filter-dropdown').css({'display' : 'none'});
            Pagination.params.column = '';
            Pagination.refreshTable();
        }
    });
}

/**
 * Filter Page Records
 * 
 * This function will filter what records are displayed in the table list.
 */
Pagination.filter = function() {
    var is = '';   

    $('.filter').on('change', function() {
        if ($('option:selected', this).val()) {
            is = $('option:selected', this).val();

            Pagination.params.is = is;
            Pagination.params.page = 1;
            
            Pagination.refreshTable();
        } else {
            Pagination.params.is = '';
            Pagination.refreshTable();
        }
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
        console.log(Pagination.params.orderby);
        // Determine the direction the table should sort by.
        if (Pagination.params.direction === 'asc') {
            Pagination.params.direction = 'desc';
        } else {
            Pagination.params.direction = 'asc';
        }
        Pagination.refreshTable();
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