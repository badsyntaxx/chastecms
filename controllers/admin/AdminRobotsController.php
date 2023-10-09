<?php 

/**
 * Admin Robots Controller Class
 *
 * This class gets and interacts with the robots.txt file and the `robots` table in the database.
 */
class AdminRobotsController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/admin/robots
     * - http://root/admin/robots/init
     */
    public function index()
    {
        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();
        
        exit($this->load->view('robots/robots', $view));
    }

    public function getRobotsData()
    {
        $robots_model = $this->load->model('robots');
        $robots = $robots_model->getRobots();

        $this->output->json($robots);
    }

    public function insertRobot()
    {
        $robots_model = $this->load->model('robots');

        $data['route'] = $_POST['route'];

        if ($robots_model->insertRobot($data)) {
            $output = ['alert' => 'success', 'message' => 'Robot inserted.'];
        } else {
            $output = ['alert' => 'error', 'message' => 'Robot not inserted.'];
        }

        $this->writeRobotFile();

        $this->output->json($output);
    }

    public function removeRobot()
    {
        $robots_model = $this->load->model('robots');
        $route = trim($_POST['route']);

        if ($robots_model->deleteRobot($route)) {
            $output = ['alert' => 'success', 'message' => 'Robot removed.'];
        } else {
            $output = ['alert' => 'error', 'message' => 'Robot not removed.'];
        }

        $this->writeRobotFile();

        $this->output->json($output);
    }

    private function writeRobotFile()
    {
        $robots_model = $this->load->model('robots');
        $routes = $robots_model->getRobots();
        $file = fopen(VIEWS_DIR . '/robots.txt', 'w+') or exit('Unable to open the robots text file!');

        if (fwrite($file, 'User-agent: *' . PHP_EOL)) {

            foreach ($routes as $r) {
                fwrite($file, 'Disallow: ' . $r['route'] . PHP_EOL);
            }

            fclose($file);
        }
    }
}