<?php 

/**
 * Admin Search Controller Class
 *
 * This SearchController class interacts with the SearchModel to find results 
 * in the database matching the search string and pushes them to the view.
 */
class AdminSearchController extends Controller
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
    public function index()
    {   
        return $this->load->view('common/search', null, 'admin');
    }

    public function liveSearch()
    {
        $users = [];
        $posts = [];
        $pages = [];

        if (!empty($_POST['string'])) {
            $string = $_POST['string'];

            // Load the search model.
            $search_model = $this->load->model('search');

            // Search these tables.
            $users = $search_model->liveSearchUsers($string);
            $posts = $search_model->liveSearchBlogPosts($string);
            $pages = $search_model->liveSearchPages($string);
        }

        $output = ['users' => $users, 'posts' => $posts, 'pages' => $pages];

        $this->output->json($output, 'exit');     
    }
}