<?php 

/**
 * Admin Settings Controller Class
 */
class AdminSettingsController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/admin/settings
     * - http://root/admin/settings/init
     *
     * This method will load settings view.
     */
    public function init()
    {
        $settings_model = $this->load->model('settings');
        
        $view['header'] = $this->load->controller('admin/header')->init();
        $view['footer'] = $this->load->controller('admin/footer')->init();
        $view['search'] = $this->load->controller('admin/search')->init();
        $view['nav'] = $this->load->controller('admin/navigation')->init();
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->init();
        $view['sitename'] = $settings_model->getSetting('sitename');
        $view['owners_email'] = $settings_model->getSetting('owners_email');
        $view['pw_setting'] = $settings_model->getSetting('strong_pw');
        $view['logout_setting'] = $settings_model->getSetting('verify_logout');
        $view['mode_setting'] = $settings_model->getSetting('maintenance_mode');
        $view['mail'] = $settings_model->getMailSettings();
        $view['languages'] = $this->getLanguages();
        $view['themes'] = $this->getThemes();

        exit($this->load->view('settings/settings', $view));
    }

    public function getLanguages()
    {
        $languages = $this->load->model('settings')->getLanguages();
        return $languages;
    }

    public function update()
    {
        $this->settings_model = $this->load->model('settings');

        $this->updateSitenameSetting();
        $this->updateOwnersEmailSetting();
        $this->updatePasswordSetting();
        $this->updateInactiveSetting();
        $this->updateLanguageSetting();
        $this->updateThemeSetting();
        $this->updateMailSettings();
        $this->updateMaintenanceSetting();
        
        $this->gusto->log('Admin "' . $this->logged_user['username'] . '" updated the site settings.');
        $output = ['alert' => 'success', 'message' => $this->language->get('settings/setting_updated')];
        $this->output->json($output, 'exit');
    }

    public function updateSitenameSetting()
    {
        $data['value'] = $_POST['sitename'];
        $data['settings_id'] = 1;

        if (!$this->settings_model->updateSetting($data)) {
            $output = ['alert' => 'error', 'message' => 'Could not update sitename setting.'];
            $this->output->json($output, 'exit');
        }
    }

    public function updateOwnersEmailSetting()
    {
        $data['value'] = $_POST['owners_email'];
        $data['settings_id'] = 2;

        if (!$this->settings_model->updateSetting($data)) {
            $output = ['alert' => 'error', 'message' => 'Could not update owners email setting.'];
            $this->output->json($output, 'exit');
        }
    }

    public function updatePasswordSetting()
    {
        $data['value'] = isset($_POST['pw_setting']) ? 1 : null;
        $data['settings_id'] = 3;

        if (!$this->settings_model->updateSetting($data)) {
            $output = ['alert' => 'error', 'message' => 'Could not update password setting.'];
            $this->output->json($output, 'exit');
        }
    }

    public function updateInactiveSetting()
    {
        $data['value'] = $_POST['inactivity_setting'];
        $data['settings_id'] = 5;

        if (!$this->settings_model->updateSetting($data)) {
            $output = ['alert' => 'error', 'message' => 'Could not update inactivity setting.'];
            $this->output->json($output, 'exit');
        }
    }

    public function updateLanguageSetting()
    {
        $data['value'] = $_POST['language_setting'];
        $data['settings_id'] = 6;

        if (!$this->settings_model->updateSetting($data)) {
            $output = ['alert' => 'error', 'message' => 'Could not update language setting.'];
            $this->output->json($output, 'exit');
        }
    }

    public function updateThemeSetting()
    {
        $data['value'] = $_POST['theme_setting'];
        $data['settings_id'] = 8;

        if (!$this->settings_model->updateSetting($data)) {
            $output = ['alert' => 'error', 'message' => 'Could not update theme setting.'];
            $this->output->json($output, 'exit');
        }
    }

    public function updateMailSettings()
    {
        $mail_host = $_POST['mail_host'];
        $mail_port = $_POST['mail_port'];
        $mail_username = $_POST['mail_username'];
        $mail_password = $_POST['mail_password'];

        $data['host'] = $mail_host;
        $data['port'] = $mail_port;
        $data['username'] = $mail_username;
        $data['password'] = $mail_password;
        $data['settings_id'] = 1;
        
        if (!$this->settings_model->updateMailSettings($data)) {
            $output = ['alert' => 'error', 'message' => 'Could not update mail settings.'];
            $this->output->json($output, 'exit');
        }
    }

    public function updateMaintenanceSetting()
    {
        $data['value'] = !empty($_POST['mode_setting']) ? 1 : null;
        $data['settings_id'] = 7;

        if (!$this->settings_model->updateSetting($data)) {
            $output = ['alert' => 'error', 'message' => 'Could not update maintenance mode setting.'];
            $this->output->json($output, 'exit');
        }
    }

    public function getThemeSetting()
    {
        $this->gusto->authenticate(3);

        $this->output->text($this->logged_user['theme']);
    }

    public function changeAdminThemeSetting() 
    {
        $this->gusto->authenticate(3);
        
        $user_model = $this->load->model('users');
        $user = $user_model->getUser('users_id', $this->session->id);
        $theme = $user['theme'];

        $data['users_id'] = $this->logged_user['users_id'];

        if ($theme == 0) $data['theme'] = 1;
        if ($theme == 1) $data['theme'] = 0;

        $user_model->updateUser($data, 'users_id');

        $this->output->text($data['theme']);
    }

    public function getMenuSetting()
    {
        $user = $this->load->model('users')->getUser('users_id', $this->session->id);
        $this->output->text($user['menu']);
    }

    public function changeMenuSetting() 
    {
        $this->gusto->authenticate(3);
        
        $user_model = $this->load->model('users');
        $user = $user_model->getUser('users_id', $this->session->id);
        $data['menu'] = $user['menu'];
        $data['users_id'] = $this->logged_user['users_id'];

        if ($data['menu'] == 0) {
            $data['menu'] = 1;
            $user_model->updateUser($data, 'users_id');
        } elseif ($data['menu'] == 1) {
            $data['menu'] = 0;
            $user_model->updateUser($data, 'users_id');
        }

        $this->output->text($data['menu']);
    }

    public function getDropdownSettings()
    {
        $inactivity = $this->load->model('settings')->getSetting('inactivity_limit');
        $language = $this->load->model('settings')->getSetting('language');
        $timezone = $this->load->model('settings')->getSetting('timezone');
        $theme = $this->load->model('settings')->getSetting('theme');

        $output = ['inactivity' => $inactivity, 'language' => $language, 'timezone' => $timezone, 'theme' => $theme];

        $this->output->json($output, 'exit');
    }

    public function getCheckboxSettings()
    {
        $maintenance = $this->load->model('settings')->getSetting('maintenance_mode');
        $password = $this->load->model('settings')->getSetting('strong_pw');
        $output = ['maintenance' => $maintenance, 'password' => $password];

        $this->output->json($output, 'exit');
    }

    public function getThemes()
    {
        $path = VIEWS_DIR . '/themes';
        $dir = new DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                if ($fileinfo != 'admin') {
                    $paths[] = $fileinfo->getFilename();
                }
            }
        }

        return $paths;
    }

    public function testEmail()
    {
        $sitename = $this->load->model('settings')->getSetting('sitename');
        $owners_email = $this->load->model('settings')->getSetting('owners_email');
        $mail_library = $this->load->library('mail');

        $mail['to'] = $owners_email;
        $mail['from'] = '';
        $mail['subject'] = $sitename . ' Test Email';
        $mail['message'] = 'Success! This is a test email from ' . $sitename;
        $mail['body'] = $mail['message'];

        // Send the contact mail and exit with failure or success message.
        if ($mail_library->sendMail($mail)) {
            $output = ['alert' => 'success', 'message' => 'The test email was sent!'];
        } else {
            $output = ['alert' => 'error', 'message' => 'The test email was not sent!'];
        }

        $this->output->json($output, 'exit');
    }
}