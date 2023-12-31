<?php 

/**
 * Header Controller Class
 *
 * The HeaderController handles logic specific to the header and displays the 
 * header view. The HeaderController should be loaded in each controller class 
 * where a header is desired.
 */
class HeaderController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Assuming that the HeaderController should be loaded in every controller 
     * this init method should run in every controller too.
     */
    public function init($params = null)
    {   
        $settings_model = $this->load->model('settings');
        $analytics = $settings_model->getAnalyticsCode();
        $sitename = $settings_model->getSetting('sitename');

        $view['links'] = $this->navigation();
        $view['logged'] = $this->session->isLogged();
        $view['theme'] = $settings_model->getSetting('theme');
        $view['sitename'] = $settings_model->getSetting('sitename');
        $view['analytics'] = $analytics;
        $view['title'] = isset($params['title']) ? $params['title'] : $sitename;
        $view['description'] = isset($params['description']) ? $params['description'] : $sitename;

        // Return the header view.
        return $this->load->view('common/header', $view);
    }

    private function navigation()
    {
        $model = $this->load->model('navigation');
        $links = $model->getNavLinks();
        $sub_links = [];
        $navigation = [];

        foreach ($links as $link) {

            $class = strtolower($link['name']);

            if (strpos($class, ' ')) {
                $class = explode(' ', $class);
                $class = implode('-', $class);
            }
                
            $link = [
                'navigation_id' => $link['navigation_id'],
                'name' => $link['name'],
                'class' => strtolower($class),
                'route' => $link['route'],
                'sort_order' => $link['sort_order'],
                'parent' => $link['parent'],
                'children' => $link['children'],
                'top' => $link['top']
            ];
        
            array_push($navigation, $link);
        }
        
        return $navigation;
    }
}