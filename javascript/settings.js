
/**
 * Settins Javascript 
 *
 * Settins js used site wide. Most of this will be used on every page.
 */
var Settings = new Object();

Settings.init = function() {
    Settings.updateLastActive();
}

Settings.updateLastActive = function() {
    $.ajax({
        url: '/account/updateLastActive',
        type: 'GET',
        success: function(response, status, xhr) {
            console.log(response);
        },
        complete: function() {
            
        }
    });
}