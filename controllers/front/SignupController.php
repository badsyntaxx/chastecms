<?php 

/**
 * Signup Controller Class
 *
 * This class handles user sign up. It saves post data from the signup form 
 * to the database and also sends the initial activation email.
 */
class SignupController extends Controller
{   
    /**
     * Access the user model.
     * @var object
     */
    private $user_model;

    /**
     * Username from signup form.
     * @var string
     */
    private $username;

    /**
     * Email from signup form.
     * @var string
     */
    private $email;

    /**
     * Hashed password ready for storage.
     * @var string
     */
    private $hashed_password;

    /**
     * Unique key created for each user.
     * @var string
     */
    private $key;

    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://gusto/signup
     * - http://gusto/signup/init
     */
    public function init()
    {      
        if ($this->session->isLogged()) $this->load->route('/home');

        $page = $this->load->model('pages')->getPage('name', 'signup');

        $data['title'] = $page['title'];
        $data['description'] = $page['description'];

        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();
        $view['sitename'] = $this->load->model('settings')->getSetting('sitename');

        $this->load->model('pages')->updatePageStatistics('signup');

        exit($this->load->view('account/signup', $view));
    }

    /**
     * Validate the signup form
     * 
     * This is the main validation method and will call all the methods 
     * necessary for the signup to complete. This method will be called
     * via an AJAX function which can be found in the signup.htm view.
     */
    public function validate()
    {   
        // Test for bots using the bot test helper.
        botTest($_POST['red_herring']);

        // Load the user model class.
        $this->user_model = $this->load->model('users');

        $this->validateUsername();
        $this->validateEmail();
        $this->validatePassword();
        $this->registerUser();
        $this->gusto->log('A new user "' . $this->username . '" registered an account.');
    }

    /**
     * Validate username
     *
     * This method will ensure that the username entered by the user is 
     * valid by removing any characters that are not a number or in the 
     * alphabet. It also checks if the username is already taken. This 
     * method should be called from the validate() method of this controller
     */
    private function validateUsername() 
    {   
        // Remove unwanted characters
        if (empty($_POST['username'])) {
            $output = ['alert' => 'error', 'message' => $this->language->get('signup/username_empty')];
            $this->output->json($output, 'exit');
        }

        $this->username = preg_replace('/[^A-Za-z0-9_-]/', '', $_POST['username']);

        // If username is greater than 20 chars, exit with error.
        if (strlen($this->username) > 20) {
            $output = ['alert' => 'error', 'message' => $this->language->get('signup/username_invalid')];
            $this->output->json($output, 'exit');
        }

        // If a matching username is found, exit with an error.
        if ($this->user_model->getUser('username', $this->username)) {
            $output = ['alert' => 'error', 'message' => $this->language->get('signup/username_taken')];
            $this->output->json($output, 'exit');
        }
    }

    /**
     * Validate user email
     * 
     * This method will ensure that the email entered by the user is valid 
     * by using the sanitize email function. It also checks if the email is 
     * already taken. This method should be called from the validate() 
     * method of this controller
     */
    private function validateEmail()
    {
        $email = $this->validate->email($_POST['email']);

        switch ($email) {
            case 'empty':
                $output = ['alert' => 'error', 'message' => $this->language->get('signup/email_empty')];
                break;
            case false:
                $output = ['alert' => 'error', 'message' => $this->language->get('signup/email_invalid')];
                break;
            default:
                if ($this->user_model->getUser('email', $email)) {
                    $output = ['alert' => 'error', 'message' => $this->language->get('signup/email_taken')];
                }
                break;
        }

        if (isset($output)) {
            $this->output->json($output, 'exit');
        }

        $this->email = $email;
    }

    /**
     * Validate user password
     * 
     * This method is used to check if the passwords entered match. If the strong password setting is on, 
     * then it will also check if the password is too weak. Then it will hash the users password and 
     * store it in a property. This method should be called from the validate() method of this controller.
     */
    private function validatePassword()
    {
        if (empty($_POST['password']) || empty($_POST['confirm'])) {
            $output = ['alert' => 'error', 'message' => $this->language->get('signup/pw_empty')];
            $this->output->json($output, 'exit');
        }

        $password = $_POST['password'];
        $confirm = $_POST['confirm'];

        if ($this->load->model('settings')->getSetting('strong_pw')) {
            $pw_strong = isPasswordStrong($password);
            if (!$pw_strong) {
                $output = ['alert' => 'error', 'message' => $this->language->get('signup/pw_weak')];
                $this->output->json($output, 'exit');
            }
        }

        if ($password !== $confirm) {
            $output = ['alert' => 'error', 'message' => $this->language->get('signup/pw_match')];
            $this->output->json($output, 'exit');
        } 

        $this->hashed_password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
    }

    /**
     * Register new user
     * 
     * This method will insert a user into the database using properties created in this controller. 
     * If the properties are not set, then the home route is loaded. This method should be called 
     * from the validate() method of this controller.
     */
    private function registerUser()
    {   
        $this->key = md5(mt_rand());

        $data['username'] = $this->username;
        $data['email'] = $this->email;
        $data['password'] = $this->hashed_password;
        $data['key'] = $this->key;
        $data['group'] = 1;
        $data['signup_date'] = date('c');
        $data['ip'] = $_SERVER['REMOTE_ADDR'];

        // Insert the user into the database.
        if ($this->user_model->insertUser($data)) {
            $this->sendActivationMail();
        } else {
            $output = ['alert' => 'error', 'message' => $this->language->get('signup/signup_fail')];
            $this->output->json($output, 'exit');
        }
    }

    /**
     * Send activation email
     *
     * This method accesses the mail library and sends activation emails to clients that sign up. 
     * This method should be called from the register method of this controller.
     */
    private function sendActivationMail()
    {
        $sitename = $this->load->model('settings')->getSetting('sitename');
        $link = HOST . '/account/activate/' . $this->key;

        $mail['to'] = $this->email;
        $mail['from'] = '';
        $mail['subject'] = 'Activate Your Account';
        $mail['message'] = $link;
        $mail['body'] = str_replace('{{link}}', $link, $this->mail->getTemplate('activate'));

        // Send the contact mail and exit with failure or success message.
        if ($this->mail->sendMail($mail)) {
            $output = ['alert' => 'success', 'message' => $this->language->get('signup/signup_success')];
        } else {
            $output = ['alert' => 'error', 'message' => $this->language->get('signup/activate_mail_fail')];
        }

        $this->output->json($output, 'exit');
    }
}