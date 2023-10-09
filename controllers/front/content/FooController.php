<?php 
 
/**
 * FooController Controller Class
 */
class FooController extends Controller
{
    /**
     * Init method
     *
     * The init method is the default for controller classes. Whenever a controller
     * class is instantiated the init method will be called.
     */
    public function index()
    {
        $page = $this->load->model('pages')->getPage('name', 'foo');
 
        $data['title'] = $page['title'];
        $data['description'] = $page['description'];
 
        $view['header'] = $this->load->controller('header')->index($data);
        $view['footer'] = $this->load->controller('footer')->index();
        $view['content'] = $this->load->model('pages')->getPageContent('foo');
 
        $this->load->model('pages')->updatePageStatistics('foo');
 
        exit($this->load->view('common/content', $view));
    }
}