<?php 

/**
 * Home Controller Class
 *
 * The HomeController class is the default controller class for the application.
 * This is the default controller for the application, so if a route is invalid 
 * or the controller cannot be found or loaded, the user will land here. 
 */
class HomeController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - /root/home
     * - /root/home/init
     */
    public function init()
    {      
        $page = $this->load->model('pages')->getPage('name', 'home');

        $data['title'] = $page['title'];
        $data['description'] = $page['description'];

        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();
        $view['content'] = $this->load->model('pages')->getPageContent('home');

        $this->load->model('pages')->updatePageStatistics('home');

        exit($this->load->view('common/home', $view));  
    }
}