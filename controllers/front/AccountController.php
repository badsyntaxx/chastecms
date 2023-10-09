<?php 

/**
 * Account Controller Class
 *
 * This class gets the users data from the database for display on the 
 * account view. It also handles edits to the account that are made by the 
 * account holder, activation and password resets.
 */
class AccountController extends Controller 
{
    /**
    * Username from account form.
    * @var string
    */
    private $username;

    /**
    * Firstname from account form.
    * @var string
    */
    private $firstname;

    /**
    * Lastname from account form.
    * @var string
    */
    private $lastname;

    /**
    * Email from account form.
    * @var string
    */
    private $email;

    /**
    * Access the mail library.
    * @var object
    */
    public $mail;

    /**
    * Access the user model.
    * @var object
    */
    private $model;
    
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/account
     * - http://root/account/init
     *
     * This init method gets user data from the database using the user session.
     * This method will also prepare the data for the account view.
     */
    public function index() 
    {   
        if ($this->logged_user['group'] < 2) {
            $this->load->route('/account/activate');
        }

        if ($this->logged_user['bio']) {
            $bio = substr($this->logged_user['bio'], 0, -200);
        } else {
            $bio = $this->language->get('account/description');
        }

        $data['title'] = $this->language->get('account/title');
        $data['description'] = $bio;

        $view['header'] = $this->load->controller('header')->index($data);
        $view['footer'] = $this->load->controller('footer')->index();        
        $view['countries'] = $this->load->model('users')->getCountries();
        
        $view['firstname'] = $this->logged_user['firstname'];
        $view['lastname'] = $this->logged_user['lastname'];
        $view['username'] = $this->logged_user['username'];
        $view['email'] = $this->logged_user['email'];
        $view['website'] = $this->logged_user['website'];

        if ($this->logged_user['birthday']) {
            $birthday = date('d-F-Y', strtotime($this->logged_user['birthday']));
            $birthday = explode('-', $birthday);

            $view['day'] = isset($birthday[0]) ? $birthday[0] : null;
            $view['month'] = isset($birthday[1]) ? $birthday[1] : null;
            $view['month_num'] = isset($birthday[1]) ? date('m', strtotime($birthday[1])) : null;
            $view['year'] = isset($birthday[2]) ? $birthday[2] : null;
        }

        if ($this->logged_user['country']) {
            $country = explode(', ', $this->logged_user['country']);
            $view['country_code'] = isset($country[0]) ? $country[0] : null;
            $view['country_name'] = isset($country[1]) ? $country[1] : null;
        }

        $view['gender'] = $this->logged_user['gender'];
        $view['privacy'] = $this->logged_user['privacy'];
        $view['avatar'] = $this->logged_user['avatar'];
        $view['bio'] = $this->logged_user['bio'];
        $view['logout_setting'] = $this->logged_user['verify_logout'];

        exit($this->load->view('account/account', $view));
    }

    public function getAccountData()
    {
        $user = $this->load->model('users')->getUser('key', $this->session->id);

        $output['username'] = $user['username'];
        $output['email'] = $user['email'];

        $this->output->json($output);
    }
    
    /**
     * Upload Avatar Image
     *
     * This method will be called by the dropzone ajax on the account page.
     * This method is responsible for validating the file and uploading it
     * to the server. It makes use of the Upload and Image libraries.
     * 
     * @return bool
     */
    public function uploadAvatar()
    {
        if (!empty($_FILES['avatar'])) {

            $upload_library = $this->load->library('Upload');
            $image_library = $this->load->library('image');
            $directory = '/views/images/uploads/account/';

            $upload_library->uploadImage($_FILES['avatar'], $directory);

            if ($upload_library->file_invalid) {
                $output = ['alert' => 'error', 'message' => $this->language->get('upload/file_invalid')];
                $this->output->json($output, 'exit');
            }
            if ($upload_library->file_big) {
                $search = ['{{filesize}}', '{{maxFilesize}}'];
                $replace = [$upload_library->file_big['size'], $upload_library->file_big['max']];
                $output = ['alert' => 'error', 'message' => str_replace($search, $replace, $this->language->get('upload/file_big'))];
                $this->output->json($output, 'exit');
            }

            if ($upload_library->upload_success) {
                $data['avatar'] = $upload_library->filename;
                $data['users_id'] = $this->logged_user['users_id'];
                
                $source = VIEWS_DIR . '/images/uploads/account/' . $upload_library->filename;
                $update = $this->load->model('users')->updateUser($data, 'users_id');
                $crop_image = $image_library->cropImage($source, 256, 256, $upload_library->filename, $directory);

                if ($update) {
                    if ($crop_image) {
                        $output = ['alert' => 'success', 'message' => $this->language->get('account/upload_success')];
                        $this->output->json($output, 'exit');
                    } else {
                        $output = ['alert' => 'error', 'message' => $this->language->get('account/upload_failure')];
                        $this->output->json($output, 'exit');
                    }
                }
            }
        }
    }

    /**
     * Validate account changes
     *
     * This method validates all changes to the users account. When users hit save
     * on the account page the form is submitted via ajax to this method.
     */
    public function validate() 
    {
        $this->model = $this->load->model('users');

        $this->validateUsername();
        $this->validateFirstname();
        $this->validateLastname();
        $this->validateEmail();
        $this->validateWebsite();
        $this->validateBirthday();
        $this->validateCountry();
        $this->validateLogout();
        $this->validatePrivacy();
        $this->validateGender();
        $this->validatePassword();
        $this->validateBio(); 

        $output = ['alert' => 'success', 'message' => $this->language->get('account/account_updated')];

        $this->output->json($output, 'exit');
    }

    /**
     * Validate username
     * 
     * Sanitizes and validates the username input record.
     */
    private function validateUsername()
    {
        if (!empty($_POST['username'])) {
            $this->username = trim(str_replace(' ', '', preg_replace('/[^A-Za-z0-9]/', '', $_POST['username'])));

            if ($this->username != $this->logged_user['username']) {

                if (strlen($this->username) > 20) {
                    exit($this->language->get('account/username_invalid'));
                }

                $user = $this->model->getUser('username', $this->username);

                // Check if the username entered is already taken.
                // Here we check if $user is falsy. If it is, we assume no user with the entered username exists.
                // If $user is not falsy then it returned a users data. So we compare the found users id with the logged users id.
                // If the ids do not match then the username is taken. If they do we found the logged user and skip ahead.
                if ($user) {
                    if ($user['users_id'] != $this->logged_user['users_id']) {
                        $output = ['alert' => 'error', 'message' => $this->language->get('account/username_taken')];
                        $this->output->json($output, 'exit');
                    }
                }

                $data['username'] = $this->username;
                $data['users_id'] = $this->logged_user['users_id'];

                if (!$this->model->updateUser($data, 'users_id')) {
                    $output = ['alert' => 'error', 'message' => $this->language->get('account/username_fail')];
                    $this->output->json($output, 'exit');
                } 
            }
        }
    }

    /**
     * Validate firstname
     *
     * Sanitizes and validates the firstname input field.
     */
    private function validateFirstname()
    {
        if (isset($_POST['firstname'])) {

            $this->firstname = preg_replace('/[^A-Za-z]/', '', trim($_POST['firstname']));

            if ($this->firstname != $this->logged_user['firstname']) {
                if (strlen($this->firstname) > 20) { 
                    exit($this->language->get('account/name_invalid')); 
                }

                $data['firstname'] = $this->firstname;
                $data['users_id'] = $this->logged_user['users_id'];

                if (!$this->model->updateUser($data, 'users_id')) {
                    $output = ['alert' => 'error', 'message' => 'Unable to update first name.'];
                    $this->output->json($output, 'exit');
                }
            }
        }
    }

    /**
     * Validate lastname
     *
     * Sanitizes and validates the lastname input field.
     */
    private function validateLastname()
    {
        if (isset($_POST['lastname'])) {

            $this->lastname = preg_replace('/[^A-Za-z]/', '', trim($_POST['lastname']));

            if ($this->lastname != $this->logged_user['lastname']) {
                if (strlen($this->lastname) > 20) { 
                    exit($this->language->get('account/name_invalid')); 
                }

                $data['lastname'] = $this->lastname;
                $data['users_id'] = $this->logged_user['users_id'];

                if (!$this->model->updateUser($data, 'users_id')) {
                    $output = ['alert' => 'error', 'message' => 'Unable to update last name.'];
                    $this->output->json($output, 'exit');
                }
            }
        }
    }

    /**
     * Validate email
     *
     * Sanitizes and validates the email input field.
     */
    private function validateEmail()
    {
        if (!empty($_POST['email'])) {
            $this->email = $this->sanitize->email($_POST['email']);

            if ($this->email != $this->logged_user['email']) {
                if (!$this->validate->email($this->email)) {
                    exit($this->language->get('account/email_invalid'));
                }

                $user = $this->model->getUser('email', $this->email);

                // Check if the email entered is already taken.
                // Here we check if $user is falsy. If it is, we assume no user with the entered email exists.
                // If $user is not falsy then it returned a users data. So we compare the found users id with the logged users id.
                // If the ids do not match then the email is taken. If they do we found the logged user and skip ahead.
                if ($user && $user['users_id'] != $this->logged_user['users_id']) {
                    $output = ['alert' => 'error', 'message' => $this->language->get('account/email_taken')];
                    $this->output->json($output, 'exit');
                } 

                $data['email'] = $this->email;
                $data['users_id'] = $this->logged_user['users_id'];

                if (!$this->model->updateUser($data, 'users_id')) {
                    $output = ['alert' => 'error', 'message' => 'Unable to update email.'];
                    $this->output->json($output, 'exit');
                }
            }
        }
    }

    /**
     * Validate website
     *
     * Sanitizes and validates the website input field.
     */
    private function validateWebsite()
    {
        if (isset($_POST['website'])) {

            $website = trim($_POST['website']);

            if ($website != $this->logged_user['website']) {
                if ($website != '') {
                    if (preg_match('/^([A-Z0-9-]+\.)+[A-Z]{2,4}$/i', $website) == false) {
                        $output = ['alert' => 'error', 'message' => $this->language->get('account/website_invalid')];
                        $this->output->json($output, 'exit');
                    }
                }

                $data['website'] = $website;
                $data['users_id'] = $this->logged_user['users_id'];

                if (!$this->model->updateUser($data, 'users_id')) {
                    $output = ['alert' => 'error', 'message' => 'Unable to update website.'];
                    $this->output->json($output, 'exit');
                }
            }
        }
    }

    /**
     * Validate birthday
     *
     * Sanitizes and validates the birthday input field.
     */
    private function validateBirthday()
    {
        if (isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year'])) {

            $day = $_POST['day'];
            $month = $_POST['month'];
            $year = $_POST['year'];
            $birthday = $year . '-' . $month . '-' . $day;

            if (!empty($day) && !empty($month) && !empty($year)) {
                if ($birthday != $this->logged_user['birthday']) {
                    if (!is_numeric($day) && !is_numeric($year)) {
                        $output = ['alert' => 'error', 'message' => $this->language->get('account/birthday_invalid')];
                        $this->output->json($output, 'exit');
                    }

                    $data['birthday'] = $birthday;
                    $data['users_id'] = $this->logged_user['users_id'];

                    if (!$this->model->updateUser($data, 'users_id')) {
                        $output = ['alert' => 'error', 'message' => 'Unable to update birthday.'];
                        $this->output->json($output, 'exit');
                    }
                }
            }
        }
    }

    private function validateCountry()
    {
        if (isset($_POST['country'])) {

            $country = $_POST['country'];

            if ($country != $this->logged_user['country']) {
                $data['country'] = $country;
                $data['users_id'] = $this->logged_user['users_id'];

                if (!$this->model->updateUser($data, 'users_id')) {
                    $output = ['alert' => 'error', 'message' => 'Unable to update country.'];
                    $this->output->json($output, 'exit');
                }
            }  
        }
    }

    public function validateLogout()
    {
        $verify_logout = isset($_POST['logout_setting']) ? 1 : 0;

        if ($verify_logout != $this->logged_user['verify_logout']) {
            $data['verify_logout'] = $verify_logout;
            $data['users_id'] = $this->logged_user['users_id'];

            if (!$this->model->updateUser($data, 'users_id')) {
                $output = ['alert' => 'error', 'message' => 'Unable to update loggout setting.'];
                $this->output->json($output, 'exit');
            }
        }
    }

    private function validatePrivacy()
    {
        $privacy = isset($_POST['privacy']) ? 1 : 0;

        if ($privacy != $this->logged_user['privacy']) {
            $data['privacy'] = $privacy;
            $data['users_id'] = $this->logged_user['users_id'];

            $update = $this->model->updateUser($data, 'users_id');
            if (!$update) {
                $output = ['alert' => 'error', 'message' => 'Unable to update privacy setting.'];
                $this->output->json($output, 'exit');
            }
        }
    }

    private function validateGender()
    {
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';

        if ($gender != $this->logged_user['gender']) {
            $data['gender'] = $gender;
            $data['users_id'] = $this->logged_user['users_id'];

            if (!$this->model->updateUser($data, 'users_id')) {
                $output = ['alert' => 'error', 'message' => 'Unable to update gender.'];
                $this->output->json($output, 'exit');
            }
        } 
    }

    private function validatePassword()
    {
        if (!empty($_POST['password']) && !empty($_POST['confirm'])) {
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];
            $password_hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

            if ($this->load->model('settings')->getSetting('strong_pw')) {
                $pw_strong = $this->helper->isPasswordStrong($password);
                if (!$pw_strong) {
                    $output = ['alert' => 'error', 'message' => $this->language->get('signup/pw_weak')];
                    $this->output->json($output, 'exit');
                }
            }

            if ($password != $confirm) {
                $output = ['alert' => 'error', 'message' => $this->language->get('account/pw_match')];
                $this->output->json($output, 'exit');
            } 

            $data['password'] = $password_hash;
            $data['users_id'] = $this->logged_user['users_id'];

            if ($this->model->updateUser($data, 'users_id')) {
                unset($_SESSION['users_id']);
                session_destroy();
                $output = ['alert' => 'success', 'message' => $this->language->get('account/pw_changed'), 'pw_changed' => 'true'];
                $this->output->json($output, 'exit');
            }
        }

        if (!empty($_POST['password']) && empty($_POST['confirm']) || empty($_POST['password']) && !empty($_POST['confirm'])) {
            $output = ['alert' => 'error', 'message' => $this->language->get('account/pw_match')];
            $this->output->json($output, 'exit');
        }
    }

    private function validateBio()
    {
        if (!empty($_POST['bio'])) {

            $bio = $this->sanitize->string($_POST['bio']);

            if ($bio != $this->logged_user['bio']) {
                $data['bio'] = $bio;
                $data['users_id'] = $this->logged_user['users_id'];

                if (!$this->model->updateUser($data, 'users_id')) {
                    $output = ['alert' => 'error', 'message' => 'Unable to update user bio.'];
                    $this->output->json($output, 'exit');
                }
            }
        }
    }

    /**
     * Activate a user account.
     * 
     * Display the activate view. If the $key param is not null, check it.
     * If the key is legit then activate the user.
     * @param string $key
     */
    public function activate($key = null)
    {
        $model = $this->load->model('users');
        $user = $model->getUser('key', $key);

        if ($this->logged_user['group'] >= 2) {
            $this->load->route('/account');
        }

        $view['header'] = $this->load->controller('header')->index();
        $view['footer'] = $this->load->controller('footer')->index();
        $view['key'] = $key;

        if (!$user) {
            $view['message'] = $this->language->get('account/key_invalid');
        } 

        $data['group'] = 2;
        $data['users_id'] = $user['users_id'];

        if ($model->updateUser($data, 'users_id')) {
            $view['message'] = ['alert' => 'success', 'message' => $this->language->get('account/activation_success')];
        } else {
            $view['message'] = ['alert' => 'error', 'message' => $this->language->get('account/activation_fail')];
        }
        
        exit($this->load->view('account/activate', $view));
    }

    /**
     * Send account activation email
     *
     * This method sends an email to activate a users account. This method will be called 
     * via an AJAX function which can be found in the account.htm view.
     */
    public function send()
    {
        $user = $this->load->model('users')->getUser('email', $this->logged_user['email']);
        $mail_library = $this->load->library('mail');
        $link = HOST . '/account/activate/' . $user['key'];

        $mail['to'] = $this->logged_user['email'];
        $mail['from'] = '';
        $mail['subject'] = 'Activate Your Account';
        $mail['message'] = $link;
        $mail['body'] = str_replace('{{link}}', $link, $this->helper->getTemplate('email/activate'));

        // Send the contact mail and exit with failure or success message.
        if ($user) {
            if ($mail_library->sendMail($mail)) {
                $output = ['alert' => 'success', 'message' => $this->language->get('account/activate_mail_sent')];
            } else {
                $output = ['alert' => 'error', 'message' => $this->language->get('account/activate_mail_fail')];
            }
        } else {
            $output = ['alert' => 'success', 'message' => $this->language->get('account/activate_mail_sent')];
        }

        $this->output->json($output, 'exit');
    }

    public function forgot()
    {
        $view['header'] = $this->load->controller('header')->index();
        $view['footer'] = $this->load->controller('footer')->index();

        exit($this->load->view('account/forgot', $view));
    }

    public function sendRecoveryMail()
    {
        $mail_library = $this->load->library('mail');
        $email = filter_var(trim(strtolower($_POST['email'])), FILTER_SANITIZE_EMAIL);
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $link = HOST . '/account/reset/' . $token;

        $mail['to'] = $email;
        $mail['from'] = '';
        $mail['subject'] = 'Reset Password';
        $mail['message'] = $link;
        $mail['body'] = str_replace('{{link}}', $link, $this->helper->getTemplate('email/reset'));

        if (!$this->validate->email($email)) {
            $output = ['alert' => 'error', 'message' => str_replace('{{email}}', $email, $this->language->get('account/email_invalid'))];
            $this->output->json($output, 'exit');
        }

        if (!$mail_library->sendMail($mail)) {
            $output = ['alert' => 'error', 'message' => str_replace('{{email}}', $email, $this->language->get('account/recovery_not_sent'))];
            $this->output->json($output, 'exit');
        }

        $data['email'] = $email;
        $data['token'] = $token;
        $data['creation_date'] = date('c');

        if ($this->load->model('users')->insertResetLink($data)) {
            $output = ['alert' => 'success', 'message' => str_replace('{{email}}', $email, $this->language->get('account/recovery_sent'))];
            $this->output->json($output, 'exit');
        }
    }

    public function reset($token = null)
    {   
        $link = $this->load->model('users')->getRecoveryLink($token);
        $user = $this->load->model('users')->getUser('email', $link['email']);

        if (!$token || !$link) {
            $this->load->route('/login');
        }

        if (time() - strtotime($link['creation_date']) > 5 * 60) {
            $this->load->model('users')->deleteRecoveryLink($token);
            $this->load->route('/login');
        } 
        
        $view['header'] = $this->load->controller('header')->index();
        $view['footer'] = $this->load->controller('footer')->index();
        $view['users_id'] = $user['users_id'];


        exit($this->load->view('account/reset', $view));
    }

    public function saveResetPassword()
    {
        if (!empty($_POST['users_id'])) {
            $id = $_POST['users_id'];
        }

        if (!empty($_POST['password']) && !empty($_POST['confirm'])) {
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];
            
            if ($this->load->model('settings')->getSetting('strong_pw')) {
                $pw_strong = $this->helper->isPasswordStrong($password);
                if (!$pw_strong) {
                    exit($this->language->get('signup/pw_weak'));
                }
            }

            if ($password != $confirm) {
                exit($this->language->get('account/pw_match'));
            } 

            $password_hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
            $model = $this->load->model('users');
            $user = $model->getUser('users_id', $id);

            $data['password'] = $password_hash;
            $data['users_id'] = $id;

            if ($model->updateUser($data, 'users_id')) {
                $this->log('User (' . $user['username'] . ') sent an activation email to ' . $user['email'] . ').');
                $output = ['alert' => 'success', 'message' => $this->language->get('account/pw_changed')];
            }
        }
        if (!empty($_POST['password']) && empty($_POST['confirm']) ||  empty($_POST['password']) && !empty($_POST['confirm'])) {
            $output = ['alert' => 'error', 'message' => $this->language->get('account/pw_match')];
        }
        $this->output->json($output, 'exit');
    }

    public function checkUsername()
    {
        if (isset($_POST)) {
            $username = trim(strtoupper($_POST['username']));
            $user = $this->load->model('users')->getUser('username', $username);
            if ($user) {
                if ($username == strtoupper($user['username'])) {
                    echo $username;
                }
            }
        }
    }

    public function checkEmail()
    {
        if (isset($_POST)) {
            $email = trim(strtolower($_POST['email']));
            $user = $this->load->model('users')->getUser('email', $email);   
            if ($user) {
                if ($email == strtolower($user['email'])) {
                    echo $email;
                }
            }
        }
    }

    public function updateLastActive()
    {
        // Update users last activity to right now.
        if ($this->session->isLogged()) {
            $data['last_active'] = date('c');
            $data['users_id'] = $this->session->id;

            $update = $this->load->model('users')->updateUser($data, 'users_id');
            if ($update) {
                echo 'User last active at ' . date('d M, Y - h:ia');
            } else {
                echo 'Users last active record could not be updated.';
            }
        }
    }
}