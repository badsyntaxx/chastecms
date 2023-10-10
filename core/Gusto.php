<?php 

/**
 * Gusto
 */
class Gusto
{   
    /**
     * Store the single instance of Gusto.
     * @var object
     */
    private static $instance;

    /**
     * Array of user data.
     * @var array
     */
    public $logged_user = null;

    /**
     * Controller classes are extended form this class so everytime you load a
     * controller class this construct will be called.
     */
    public function __construct()
    {
        $this->load = new Loader();
        $cores = $this->load->cores();
        foreach ($cores as $core) {
            $name = strtolower($core);
            if (class_exists($core)) {
                $this->$name = new $core();
            }
        }

        if ($this->session->isLogged()) {
            $this->logged_user = $this->load->model('users')->getUser('users_id', $this->session->id);
        }

        if (isAdmin()) {
            $this->authenticate(3);
        }

        $this->checkForMaintenanceMode();
    }

    /**
     * Create a static instance of this class.
     * 
     * @return object
     */
    public static function getInstance() 
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Authenticate the session by determining the users group level.
     * 
     * @param integer - $group - The minimum group level the user must have to not be re-routed.
     * @param string - $route - The route to send the user to if they dont meet the level requirement.
     */
    public function authenticate($group = 1, $route = '/home')
    {
        if ($this->session->isLogged()) {
            $user = $this->load->model('users')->getUser('users_id', $this->session->id);
            if ($user['group'] < $group) {
                $this->load->route($route);
            }
        } else {
            $this->load->route($route); 
        }        
    }

    /**
     * Chech for maintenance mode by getting the setting from the settings table. If maintenance mode
     * is on, normal users will be routed to the maintenance view. Administrators will still be able
     * to view all pages and see the site as they normal would.
     */
    public function checkForMaintenanceMode()
    {
        $session = $this->session->isLogged();
        $maintenance = $this->load->model('settings')->getSetting('maintenance_mode');
        $user = isset($this->session->id) ? $this->load->model('users')->getUser('users_id', $this->session->id) : null;
        $url = isset($_GET['url']) ? splitUrl($_GET['url']) : null;

        if ($maintenance) {
            if (!$user || $user['group'] < 4) {
                if ($url[0] != 'login' && $url[0] != 'logout') {
                    $view['message'] = $this->language->get('maintenance/maintenance_mode');
                    $view['theme'] = $this->load->model('settings')->getSetting('theme');

                    exit($this->load->view('information/maintenance', $view));
                }
            }
        }
    }

    /**
     * Log messages by inserting them into the logs table in the database.
     * 
     * @param string - $message - The message to be inserted.
     */
    public function log($message)
    {
        $this->load->model('log')->insertLog($message);
    }
}