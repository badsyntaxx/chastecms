<?php 

/**
 * Footer Controller Class
 * 
 * The FooterController handles logic specific to the footer and displays the 
 * footer view. The FooterController should be loaded in each controller class 
 * where a footer is desired.
 */
class FooterController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     * 
     * Assuming that the FooterController should be loaded in every controller 
     * this init method should run in every controller too.
     */
    public function init()
    {     
        $view['links'] = $this->navigation();

        return $this->load->view('common/footer', $view);
    }

    private function navigation()
    {
        $model = $this->load->model('navigation');
        $links = $model->getNavLinks();
        $sub_links = [];
        $navigation = [];

        foreach ($links as $link) {

            $class = strtolower($link['name']);

            if (strpos($link['name'], ' ')) {
                $class = explode(' ', $link['name']);
                foreach ($class as $key => $value) {
                    $adjusted_name[$key] = strtolower($value);
                }
                $class = implode('-', $adjusted_name);
            }
                
            $link = [
                'navigation_id' => $link['navigation_id'],
                'name' => $link['name'],
                'class' => $class,
                'route' => $link['route'],
                'sort_order' => $link['sort_order'],
                'parent' => $link['parent'],
                'children' => $link['children'],
                'bottom' => $link['bottom']
            ];
        
            array_push($navigation, $link);
        }

        return $navigation;
    }
}