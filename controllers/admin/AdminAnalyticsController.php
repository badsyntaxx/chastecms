<?php 

/**
 * Admin Analytics Controller Class
 */
class AdminAnalyticsController extends Controller
{
    public function index()
    {
        $analytics_model = $this->load->model('analytics');
        $analytics_code = $analytics_model->getAnalyticsCode();
        
        $view['analytics_code'] = $analytics_code['code'];
        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();

        exit($this->load->view('analytics/analytics', $view));
    }

    public function validate()
    {
        $code = trim($_POST['code']);
        $data['code'] = $code;
        $data['analytics_id'] = 1;

        if ($this->load->model('analytics')->updateAnalyticsCode($data)) {
            $this->log('Admin "' . $this->logged_user['username'] . '" added google analytics to the site.');
            $output = ['alert' => 'success', 'message' => $this->language->get('analytics/code_updated')];
        } else {
            $output = ['alert' => 'error', 'message' => $this->language->get('analytics/code_not_updated')];
        }

        $this->output->json($output, 'exit');
    }
}