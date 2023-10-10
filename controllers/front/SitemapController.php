<?php 

/**
 * Sitemap Controller Class
 *
 * The sitemap controller is responsible for finding all the links in the host. 
 * It also creates an xml version of the sitemap and prepares the data to be 
 * displayed in the view and saved to the database.
 */
class SitemapController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/sitemap
     * - http://root/sitemap/init
     *
     * This method gets sitelinks from the database for display in the sitemap view.
     */
    public function init()
    {  
        $sitelinks = $this->load->model('sitemap')->getVisibleSiteLinks();

        $data['title'] = $this->language->get('sitemap/title');
        $data['description'] = $this->language->get('sitemap/description');

        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();
        $view['pages'] = $sitelinks ? $sitelinks : [];

        exit($this->load->view('information/sitemap', $view));
    }
}