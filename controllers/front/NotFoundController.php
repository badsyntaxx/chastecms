<?php 

/**
 * Not Found Controller Class
 */
class NotFoundController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/not-found
     * - http://root/not-found/init
     */
    public function index()
    {     
        $url = isset($_GET['url']) ? $this->helper->splitUrl($_GET['url']) : null;

        if ($url[0] == 'admin') {
            $this->load->route('/admin/dashboard');
        }

        $data['title'] = str_replace('{{page}}', implode('/', $url), $this->language->get('not_found/title'));
        $data['description'] = $this->language->get('not_found/description');

        $view['url'] = isset($url) ? implode('/', $url) : '';
        $view['header'] = $this->load->controller('header')->index($data);
        $view['footer'] = $this->load->controller('footer')->index();

        exit($this->load->view('information/not-found', $view));  
    }
}