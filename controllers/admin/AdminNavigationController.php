<?php 

/**
 * Admin Navigation Controller Class
 *
 * The NavigationController handles logic specific to the header and displays the 
 * header view. The NavigationController should be loaded in each controller class 
 * where a header is desired.
 */
class AdminNavigationController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Assuming that the NavigationController should be loaded in every controller 
     * this init method should run in every controller too.
     */
    public function index()
    {   
        if ($this->logged_user) {
            $menu = (int)$this->load->model('users')->getUserMenuSetting($this->logged_user['users_id']);
        }

        $menu_setting = '';
        
        if ($menu == 1) {
            $menu_setting = 'menu-open';
        }

        $this->session->createSession('main_nav', $menu_setting, true);
        
        $view['nav_text_dashboard'] = $this->language->get('nav/nav_text_dashboard');
        $view['nav_text_users'] = $this->language->get('nav/nav_text_users');
        $view['nav_text_pages'] = $this->language->get('nav/nav_text_pages');
        $view['nav_text_sitemap'] = $this->language->get('nav/nav_text_sitemap');
        $view['nav_text_blog'] = $this->language->get('nav/nav_text_blog');
        $view['nav_text_modules'] = $this->language->get('nav/nav_text_modules');
        $view['nav_text_analytics'] = $this->language->get('nav/nav_text_analytics');
        $view['nav_text_robots'] = $this->language->get('nav/nav_text_robots');
        $view['nav_text_settings'] = $this->language->get('nav/nav_text_settings');
        $view['nav_text_logout'] = $this->language->get('nav/nav_text_logout');
        $view['main_nav'] = $this->session->getSession('main_nav');

        return $this->load->view('common/nav', $view);
    }
}