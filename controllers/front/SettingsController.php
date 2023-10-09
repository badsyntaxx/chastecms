<?php 

/**
 * Settings Controller Class
 *
 * The class will apply settings.
 */
class SettingsController extends Controller
{
    /**
     * Get the inactiviy limit.
     *
     * This method is called by ajax /application/views/js/activity.js.
     *
     * Expected output should be an int between 0 and 60.
     * The int represents minutes.
     */
    public function getInactivityLimit()
    {
        $inactive_limit = (int) $this->load->model('settings')->getSetting('inactivity_limit');
        echo json_encode($inactive_limit);
    }
}