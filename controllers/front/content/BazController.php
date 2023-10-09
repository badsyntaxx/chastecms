<?php 
 
/**
 * BazController Controller Class
 */
class BazController extends Controller
{
    /**
     * Init method
     *
     * The init method is the default for controller classes. Whenever a controller
     * class is instantiated the init method will be called.
     */
    public function index()
    {
        $page = $this->load->model('pages')->getPage('name', 'baz');
 
        $data['title'] = $page['title'];
        $data['description'] = $page['description'];
 
        $view['header'] = $this->load->controller('header')->index($data);
        $view['footer'] = $this->load->controller('footer')->index();
        $view['content'] = $this->load->model('pages')->getPageContent('baz');
 
        $this->load->model('pages')->updatePageStatistics('baz');
 
        exit($this->load->view('common/content', $view));
    }
}