
/**
 * Activity checker
 *
 * Check how long it's been since the users last activity
 * and if the time is past the time limit setting, log 
 * the user out.
 */
var idle_time = 0;
$(document).ready(function () {
    //Increment the idle time counter every minute.
    var interval = setInterval(checkTime, 60000); // 1 Min

    //Zero the idle timer on mouse movement or keypress.
    $(this).mousemove(function(e) {
        idle_time = 0;
    });
    $(this).keypress(function(e) {
        idle_time = 0;
    });
});

function checkTime() {
    $.ajax({
        url: '/settings/getInactivityLimit',
        type: 'GET',
        success: function(response, status, xhr) {
            if ($.trim(response)) {
                var data = JSON.parse(response);
                if (data !== 0) {
                    idle_time = idle_time + 1;
                    if (idle_time > data) { // 2 minutes
                        window.location.replace('/logout/inactive');
                    }   
                }
            }
        }
    });
}