<?php 

/**
 * Dashboard Controller Class
 */
class AdminDashboardController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/dashboard
     * - http://root/dashboard/init
     *
     * The DashboardController class is the default controller for the application. 
     * This means that invalid routes will default to this init method.
     */
    public function index()
    {   
        $users = $this->getUserInfo();
        $pages = $this->getPagesInfo();
        $blog = $this->getBlogInfo();
        $settings = $this->getSettingsInfo();

        foreach ($users as $key => $value) {
            $view[$key] = $value;
        }

        foreach ($pages as $key => $value) {
            $view[$key] = $value;
        }

        foreach ($blog as $key => $value) {
            $view[$key] = $value;
        }

        foreach ($settings as $key => $value) {
            $view[$key] = $value;
        }

        $view['logs'] = [];
        $logs = $this->load->model('log')->getLogs();

        if (!empty($logs)) {
            foreach ($logs as $log) {
                $view['logs'][] = [
                    'log_id' => $log['log_id'],
                    'time' => date('d/m/Y h:ia', strtotime($log['time'])),
                    'event' => $log['event']
                ];
            }
        } 

        $view['errors'] = [];
        $errors = $this->load->model('log')->getErrors();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $view['errors'][] = [
                    'errors_id' => $error['errors_id'],
                    'time' => date('d/m/Y h:ia', strtotime($error['time'])),
                    'event' => $error['event']
                ];
            }
        } 

        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();

        exit($this->load->view('common/home', $view));
    }

    private function getUserInfo()
    {
        $model = $this->load->model('users');
        $latest = $model->getLatestRegistrant();
        $total_users = $model->getTotalUsersNumber();
        $registered = $model->getUsersByGroup(2);
        $mods = $model->getUsersByGroup(3);
        $admins = $model->getUsersByGroup(4);
        $banned = $model->getUsersByGroup(0);

        $data['total_users'] = $total_users ? $total_users : 0;
        $data['registered'] = $registered ? $registered : 0;
        $data['mods'] = $mods ? $mods : 0;
        $data['admins'] = $admins ? $admins : 0;
        $data['banned'] = $banned ? $banned : 0;
        $data['latest_reg'] = $latest['username'];

        return $data;
    }

    private function getPagesInfo()
    {
        $model = $this->load->model('pages');
        $most_viewed = $model->getMostViewed();
        $last_made = $model->getLastPageMade();
        $total_views = $model->getTotalPageViews();
        $last_edit = $last_edit = $model->getLastEdited();

        $data['total_pages'] = $model->getTotalPageNumber();
        $data['mv_page_title'] = $most_viewed['title'];
        $data['mv_page_views'] = $most_viewed['views'];
        $data['mv_page_name'] = $most_viewed['name'];
        $data['lpm_id'] = $last_made['pages_id'];
        $data['lpm_title'] = $last_made['title']; 
        $data['lpm_name'] = $last_made['name'];
        $data['page_views'] = $total_views ? $total_views : 0;
        $data['lpe_title'] = $last_edit['title'];
        $data['lpe_id'] = $last_edit['pages_id'];
        $data['lpe_name'] = $last_edit['name'];

        return $data;
    }

    private function getBlogInfo() 
    { 
        $model = $this->load->model('blog');
        $most_viewed = $model->getMostViewed();
        $last_post = $model->getLastPost();
        $last_edit = $model->getLastEdited();
        $total_posts = $model->getTotalPostsNumber();
        $total_views = $model->getViewsNumber();

        $data['total_posts'] = $total_posts ? $total_posts : 0;
        $data['blog_views'] = $total_views ? $total_views : 0;
        $data['mv_blog_title'] = $most_viewed['title'];
        $data['mv_title'] = $most_viewed['title'];
        $data['mv_title'] = str_replace(' ', '_', $most_viewed['title']);
        $data['mv_id'] = $most_viewed['blog_id'];
        $data['mv_blog_route'] = '/admin/blog/' . $most_viewed['blog_id'] . '/edit/'; 
        $data['lp_title'] = $last_post['title'];         
        $data['lp_id'] = $last_post['blog_id'];
        $data['le_title'] = $last_edit['title'];
        $data['le_id'] = $last_edit['blog_id'];

        return $data;
    }

    private function getSettingsInfo()
    {
        $model = $this->load->model('settings');
        $maintenance = $this->load->model('settings')->getSetting('maintenance_mode');
        $lang = $this->load->model('settings')->getSetting('language');

        $data['sitename'] = $this->load->model('settings')->getSetting('sitename');
        $data['language'] = ucfirst($lang);
        $data['theme'] = $this->load->model('settings')->getSetting('theme');
        $data['maintenance'] = $maintenance == 1 ? 'On' : 'Off';

        return $data;
    }

    public function clearLog()
    {
        if ($this->load->model('log')->clearLog()) {
            $output['alert'] = 'success';
            $output['message'] = $this->language->get('settings/logs_cleared');
            $this->output->json($output, 'exit');
        }
    }

    public function clearErrors()
    {
        if ($this->load->model('log')->clearErrors()) {
            $output['alert'] = 'success';
            $output['message'] = $this->language->get('settings/errors_cleared');
            $this->output->json($output, 'exit');
        }
    }
}