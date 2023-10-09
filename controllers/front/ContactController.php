<?php 

/**
 * Contact Controller Class
 *
 * This controller class displays the contact view, which contains the main
 * contact form. This class will also validate the post data and send the 
 * contact email.
 */
class ContactController extends Controller
{
    /**
     * Init method
     *
     * The init method is the default for controller classes. Whenever a controller 
     * class is instantiated the init method will be called.
     * 
     * Routes
     *  - http://root/contact
     *  - http://root/contact/init
     */
    public function index()
    {   
        $page = $this->load->model('pages')->getPage('name', 'contact');

        $data['title'] = $page['title'];
        $data['description'] = $page['description'];

        $view['header'] = $this->load->controller('header')->index($data);
        $view['footer'] = $this->load->controller('footer')->index();
        $view['content'] = $this->load->model('pages')->getPageContent('contact');

        $this->load->model('pages')->updatePageStatistics('contact');

        exit($this->load->view('information/contact', $view));
    }

    /**
     * Validate the contact form
     * 
     * The post data is submitted by ajax in the contact view. If the 
     * data is valid this method will also access the mail library
     * and send a contact email to the site owner.
     */
    public function validate()
    {
        $this->helper->botTest($_POST['red_herring']);

        $sitename = $this->load->model('settings')->getSetting('sitename');
        $owners_email = $this->load->model('settings')->getSetting('owners_email');
        $search = [];
        $replace = [];

        if (!empty($_POST['name'])) {
            $p['name'] = trim($_POST['name']);
        }
        if (!empty($_POST['firstname'])) {
            $p['firstname'] = trim($_POST['firstname']);
        }
        if (!empty($_POST['lastname'])) {
            $p['lastname'] = trim($_POST['lastname']);
        }
        if (!empty($_POST['company'])) {
            $p['company'] = trim($_POST['company']);
        }
        if (!empty($_POST['phone'])) {
            $p['phone'] = trim($_POST['phone']);
        }
        if (!empty($_POST['fax'])) {
            $p['fax'] = trim($_POST['fax']);
        }
        if (!empty($_POST['address'])) {
            $p['address'] = trim($_POST['address']);
        }
        if (!empty($_POST['website'])) {
            $p['website'] = trim($_POST['website']);
        }
        if (!empty($_POST['date'])) {
            $p['date'] = trim($_POST['date']);
        }
        if (!empty($_POST['time'])) {
            $p['time'] = trim($_POST['time']);
        }
        if (!empty($_POST['email'])) {
            $p['email'] = filter_var(trim(strtolower($_POST['email'])), FILTER_SANITIZE_EMAIL);
        }
        if (!empty($_POST['subject'])) {
            $p['subject'] = trim($_POST['subject']);
        } else {
            $p['subject'] = $sitename . ' Contact Form';
        }
        if (!empty($_POST['message'])) {
            $p['message'] = trim($_POST['message']);
        }

        foreach ($p as $key => $value) {
            array_push($search, '{{' . $key . '}}');
            array_push($replace, $value);
        }

        $text = '<h1 class="null">' . $sitename . ' Contact Form</h1>';

        if (isset($p['name'])) {
            $text .= '<strong>Name:</strong> {{name}}<br>';
        }
        if (isset($p['firstname'])) {
            $text .= '<strong>First Name:</strong> {{firstname}}<br>';
        }
        if (isset($p['lastname'])) {
            $text .= '<strong>Last Name:</strong> {{lastname}}<br>';
        }
        if (isset($p['company'])) {
            $text .= '<strong>Company:</strong> {{company}}<br>';
        }
        if (isset($p['phone'])) {
            $text .= '<strong>Phone:</strong> {{phone}}<br>';
        }
        if (isset($p['fax'])) {
            $text .= '<strong>Fax:</strong> {{fax}}<br>';
        }
        if (isset($p['address'])) {
            $text .= '<strong>Address:</strong> {{address}}<br>';
        }
        if (isset($p['website'])) {
            $text .= '<strong>Website:</strong> {{website}}<br>';
        }
        if (isset($p['date'])) {
            $text .= '<strong>Date:</strong> {{date}}<br>';
        }
        if (isset($p['time'])) {
            $text .= '<strong>Date:</strong> {{time}}<br>';
        }
        if (isset($p['email'])) {
            $text .= '<strong>Email:</strong> {{email}}<br>';
        }
        if (isset($p['subject'])) {
            $text .= '<strong>Subject:</strong> {{subject}}<br>';
        }
        if (isset($p['message'])) {
            $text .= '<strong>Message:</strong> {{message}}<br>';
        }

        $output = str_replace($search, $replace, $text);
        $body = str_replace('{{main_text}}', $output, $this->helper->getTemplate('email/contact'));

        $mail['to'] = $owners_email;
        $mail['from'] = '';
        $mail['subject'] = $p['subject'];
        $mail['message'] = $p['message'];
        $mail['body'] = $body;

        // Send the contact mail and exit with failure or success message.
        if ($this->mail->sendMail($mail)) {
            $this->log('A user used the contact form with the email address (' . $p['email'] . ').');
            $output = ['alert' => 'success', 'message' => str_replace('{{sitename}}', $sitename, $this->language->get('contact/contact_success'))];
            $this->output->json($output);
        } else {
            $this->log('A user attempted to use the contact form with the email address (' . $p['email'] . ').');
            $output = ['alert' => 'error', 'message' => $this->language->get('contact/contact_fail')];
            $this->output->json($output);
        }
    }
}