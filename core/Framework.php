<?php

/**
 * Framework Core Class
 * 
 * The core framework of the application.
 */
class Framework
{
    /**
     * Store the single instance of the framework.
     * @var object
     */
    private static $instance;

    public $session;
    public $load;
    public $helper;
    public $language;

    public static $core_properties = [];

    /**
     * Framework Construct
     * 
     * Load Chaste library files and create objectes for use throughout the applicaton.
     * The construct will also call all methods in this class excluding those in the 
     * exclude array. So, If you need a universal method to be called each time a page
     * is loaded this class is the place to put it.
     * 
     * @return void
     */
    public function __construct()
    {
        $cores = [
            'helper' => 'Helpers', 
            'language' => 'Language', 
            'load' => 'Loader', 
            'mail' => 'Mail', 
            'output' => 'Output', 
            'sanitize' => 'Sanitize', 
            'session' =>  'Session', 
            'validate' => 'Validate'
        ];

        foreach ($cores as $property => $class) {
            if (class_exists($class)) {
                $this->$property = new $class();
                self::$core_properties[$property] = $this->$property;
            }
        }

        self::$core_properties['logged_user'] = null;
        
        if (Authenticate::getInstance($this->session, $this->load)) {
            if ($this->session->isLogged()) {
                self::$core_properties['logged_user'] = $this->load->model('users')->getUser('key', $this->session->id);
            }
        } 

        foreach (get_class_methods($this) as $method) {
            if (!in_array($method, ['__construct', 'getInstance'])) {
                $this->$method();
            } 
        }

        Router::getInstance();
    }

    public static function getCores()
    {
        return self::$core_properties;
    }

    /**
     * Check if maintenance mode is on
     * 
     * Chech for maintenance mode by getting the setting from the settings table. If maintenance mode
     * is on, normal users will be routed to the maintenance view. Administrators will still be able
     * to view all pages.
     * 
     * @return void
     */
    public function isMaintenance()
    {
        if ($this->session->isLogged()) {
            $maintenance = $this->load->model('settings')->getSetting('maintenance_mode');
            $user = isset($this->session->id) ? $this->load->model('users')->getUser('key', $this->session->id) : null;
            $url = isset($_GET['url']) ? $this->helper->splitUrl($_GET['url']) : null;

            if ($maintenance) {
                if (!$user || $user['group'] < 3) {
                    if ($url[0] != 'login' && $url[0] != 'logout') {
                        $view['message'] = $this->language->get('maintenance/maintenance_mode');
                        exit($this->load->view('information/maintenance', $view));
                    }
                }
            }
        }
    }

    /**
     * Check user activity
     * 
     * If a user is logged in, check their activity and update their active time.
     * We do this so we know if a user is online.
     *
     * @return void
     */
    public function isActivity()
    {
        if ($this->session->isLogged()) {
            $data['last_active'] = date('c');
            $data['key'] = $this->session->id;
            $this->load->model('users')->updateUser($data, 'key');
        }
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

}