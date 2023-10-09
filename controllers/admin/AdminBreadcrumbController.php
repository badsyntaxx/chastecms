<?php 

/**
 * Admin Breadcrumb Controller Class
 *
 * This class creates a breadcrumb array for use in the view.
 */
class AdminBreadcrumbController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * no route access to this controller
     *
     * This init method will create an array using the url. This method will also
     * create a breadcrumb view. 
     */
    public function index()
    {
        $view['breadcrumbs'] = [];

        $links = isset($_GET['url']) ? $this->helper->splitUrl($_GET['url']) : null;

        if ($links) {
            $paths = [];
            
            while (!empty($links)) {
                array_push($paths, implode('/', $links));
                array_pop($links);
            }

            sort($paths);
            foreach ($paths as $path) {
                $path_array = explode('/', $path);

                $view['breadcrumbs'][] = [
                    'link' => $path,
                    'crumb' => str_replace('-', ' ', strtoupper(end( $path_array)))
                ];
            }
        }

        return $this->load->view('common/breadcrumb', $view);
    }
}