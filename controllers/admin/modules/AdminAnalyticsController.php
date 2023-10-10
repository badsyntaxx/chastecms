<?php 

/**
 * Admin Analytics Controller Class
 */
class AdminAnalyticsController extends Controller
{
    public function init()
    {
        $analytics_model = $this->load->model('analytics');
        $analytics_code = $analytics_model->getAnalyticsCode();
        
        $view['analytics_code'] = $analytics_code['code'];
        $view['header'] = $this->load->controller('admin/header')->init();
        $view['footer'] = $this->load->controller('admin/footer')->init();
        $view['search'] = $this->load->controller('admin/search')->init();
        $view['nav'] = $this->load->controller('admin/navigation')->init();
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->init();

        exit($this->load->view('modules/analytics', $view));
    }

    public function validate()
    {
        $code = trim($_POST['code']);
        $data['code'] = $code;
        $data['analytics_id'] = 1;

        if ($this->load->model('analytics')->updateAnalyticsCode($data)) {
            $this->gusto->log('Admin "' . $this->logged_user['username'] . '" added google analytics to the site.');
            $output = ['alert' => 'success', 'message' => $this->language->get('analytics/code_updated')];
        } else {
            $output = ['alert' => 'error', 'message' => $this->language->get('analytics/code_not_updated')];
        }

        $this->output->json($output, 'exit');
    }
}