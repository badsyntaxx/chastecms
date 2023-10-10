<?php 
 
/**
 * TermsAndConditionsController Controller Class
 */
class TermsAndConditionsController extends Controller
{
    /**
     * Init method
     *
     * The init method is the default for controller classes. Whenever a controller
     * class is instantiated the init method will be called.
     */
    public function init()
    {
        $page = $this->load->model('pages')->getPage('name', 'terms-and-conditions');
 
        $data['title'] = $page['title'];
        $data['description'] = $page['description'];
 
        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();
        $view['content'] = $this->load->model('pages')->getPageContent('terms-and-conditions');
 
        $this->load->model('pages')->updatePageStatistics('terms-and-conditions');
 
        exit($this->load->view('common/content', $view));
    }
}