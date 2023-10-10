<?php 

/**
 * Search Controller Class
 *
 * This SearchController class interacts with the SearchModel to find results 
 * in the database matching the search string and pushes them to the view.
 */
class SearchController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/search
     * - http://root/search/init
     * 
     * @param string $string      
     */
    public function init($string = null)
    {   
        $data['page'] = 'search';
        $data['title'] = 'Search: ' . $string;
        $data['description'] = '';

        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();
        $view['string'] = $string;

        if ($string) {
            // Load the search model.
            $search_model = $this->load->model('search');
            
            // Search these tables.
            $view['users'] = $search_model->searchUsers($string);
            $view['posts'] = $search_model->searchBlog($string);
        }

        exit($this->load->view('common/search', $view));
    }
}