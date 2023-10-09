<?php 

/**
 * Logout Controller Class
 *
 * The Logout controller will verify logouts and logout inactive users.
 */
class LogoutController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/logout
     * - http://root/logout/init
     *
     * This init method uses the action parameter to decide what kind of
     * logout is to take place.
     *
     * @param string
     */
    public function index($action = null)
    {   
        $inactive = false;

        if ($this->logged_user['verify_logout'] == 0) {
            $log = 'User (' . $this->logged_user['username'] . ') logged out.';
            if ($this->destroySession($log)) $this->load->route('/home');
        }

        if ($action == 'confirm') {
            $log = 'User (' . $this->logged_user['username'] . ') logged out.';
            if ($this->destroySession($log)) $this->load->route('/home');          
        }

        if ($action == 'inactive') {
            if ($this->logged_user) {
                $inactive = true; 
                $log = 'User (' . $this->logged_user['username'] . ') was logged out due to inactivity.';
                $this->destroySession($log);
            }
        }

        $view['header'] = $this->load->controller('header')->index();
        $view['footer'] = $this->load->controller('footer')->index();
        $view['inactive'] = $inactive;

        exit($this->load->view('account/logout', $view)); 
    }

    /**
     * Destroy User Session
     *
     * Destroy the session, logging the user out. After the session is destroyed redirect 
     * them to the homepage. This method wil be called by this classes init.
     */
    public function destroySession($log)
    {
        unset($_SESSION['id']);
        session_destroy();
        return true;
    }
}