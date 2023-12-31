<?php 

/**
 * Admin Header Controller Class
 *
 * The HeaderController handles logic specific to the header and displays the 
 * header view. The HeaderController should be loaded in each controller class 
 * where a header is desired.
 */
class AdminHeaderController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Assuming that the HeaderController should be loaded in every controller 
     * this init method should run in every controller too.
     */
    public function init()
    {    
        $url = isset($_GET['url']) ? splitUrl($_GET['url']) : null;

        if (isset($url[1])) {
            $view['title'] = $this->language->get($url[1] . '/title');
        } else {
            $view['title'] = 'Overview';
        }

        $view['theme'] = $this->load->model('settings')->getSetting('theme');
        $view['logged'] = $this->logged_user;
        $view['theme_text'] = $this->language->get('header/theme_text');
        $view['view_text'] = $this->language->get('header/view_text');
        $view['account_text'] = $this->language->get('header/account_text');

        return $this->load->view('common/header', $view);
    }
}