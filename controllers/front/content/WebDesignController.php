<?php 
 
/**
 * WebDesignController Controller Class
 */
class WebDesignController extends Controller
{
    /**
     * Init method
     *
     * The init method is the default for controller classes. Whenever a controller
     * class is instantiated the init method will be called.
     */
    public function init()
    {
        $page = $this->load->model('pages')->getPage('name', 'web-design');
 
        $data['title'] = $page['title'];
        $data['description'] = $page['description'];
 
        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();
        $view['content'] = $this->load->model('pages')->getPageContent('web-design');
 
        $this->load->model('pages')->updatePageStatistics('web-design');
 
        exit($this->load->view('common/content', $view));
    }
}