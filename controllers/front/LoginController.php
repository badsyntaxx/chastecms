<?php 

/**
 * Login Controller Class
 *
 * The login controller class validates user logins and creates sessions to 
 * track users that have logged in. It also tracks failed login attempts.
 */
class LoginController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://gusto/login
     * - http://gusto/login/init
     */
    public function init()
    {       
        if ($this->session->isLogged()) $this->load->route('/home');

        $page = $this->load->model('pages')->getPage('name', 'login');

        $data['title'] = $page['title'];
        $data['description'] = $page['description'];

        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();
        $view['content'] = $this->load->model('pages')->getPageContent('login');
        $view['sitename'] = $this->load->model('settings')->getSetting('sitename');

        $this->load->model('pages')->updatePageStatistics('login');

        exit($this->load->view('account/login', $view));
    }

    /**
     * Validate the login form
     *
     * The post data is submitted by ajax in the login view. If the data is 
     * valid this method will also access the session library to create a 
     * session and log the user in.
     */
    public function validate()
    {   
        // Test for bots using the bot test helper.
        botTest($_POST['red_herring']);

        $this->email = $this->sanitize->email($_POST['email']);
        $this->password = $_POST['password'];
        $this->user_model = $this->load->model('users');
        $this->user = $this->user_model->getUser('email', $this->email);
        $this->time = date('c');
        $this->ip = $_SERVER['REMOTE_ADDR'];

        if (!$this->user) {
            $output = ['alert' => 'error', 'message' => $this->language->get('login/login_fail')];
            $this->output->json($output, 'exit');
        }

        if ($this->ipIsBanned() || $this->userIsBanned()) {
            $output = ['alert' => 'error', 'message' => $this->language->get('login/locked')];
            $this->output->json($output, 'exit');
        }

        // If the password is wrong, update some records to show the login attempt.
        if (!password_verify($this->password, $this->user['password'])) {
            $this->logAttempts();
            $output = ['alert' => 'error', 'message' => $this->language->get('login/login_fail')];
            $this->output->json($output, 'exit');
        }

        $data['ip'] = $this->ip;
        $data['users_id'] = $this->user['users_id'];

        $this->user_model->updateUser($data, 'users_id');
        $this->user_model->deleteLoginAttempts($this->email);
        $this->session->createSession('id', $this->user['users_id']);
        
        $output = ['alert' => 'success', 'message' => ''];
        $this->output->json($output, 'exit');
    }

    private function logAttempts()
    {
        $data['email'] = $this->email;
        $data['lock_time'] = $this->time;
        $data['ip'] = $this->ip;

        $attempts = $this->user_model->getLoginAttempts($this->ip);
        if (!$attempts) {  
            $data['attempts'] = 1;
            $this->user_model->insertLoginAttempt($data);
        } else {
            $data['attempts'] = ++$attempts['attempts'];
            $update = $this->user_model->updateLoginAttempts($data);
        }            
    }

    private function ipIsBanned() 
    {
        $attempts = $this->user_model->getLoginAttempts($this->ip);
        if ($attempts) {
            if ($attempts['email'] == $this->email) {
                if ($attempts['attempts'] > 10) {
                    $data['email'] = $this->email;
                    $data['group'] = 66;
                    $this->user_model->updateUser($data, 'email');
                    return true;
                }
            }
        }
        return false;
    }

    private function userIsBanned() 
    {
        if ($this->user['group'] == 66) {
            return true;
        }
        return false;
    }
}