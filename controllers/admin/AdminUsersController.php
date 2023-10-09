<?php 

/**
 * Admin Users Controller Class
 *
 * This class gets users information and has the ability to alter a users status.
 */
class AdminUsersController extends Controller
{
    /**
     * Init method
     *
     * The index methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/admin/users
     * - http://root/admin/users/index
     *
     * This method will load the users list table.
     */
    public function index()
    {
        $data = $this->prepareTable();

        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();
        $view['controls'] = $this->load->view('users/controls');
        $view['filters'] = $this->load->view('users/filters');
        $view['list'] = $data['list'];
        $view['table'] = $data['table'];
        $view['orderby'] = $data['orderby'];
        $view['direction'] = $data['direction'];
        $view['page'] = $data['page'];
        $view['start'] = $data['start'];
        $view['record_limit'] = $data['record_limit'];
        $view['total_pages'] = $data['total_pages'];
        $view['total_records'] = $data['total_records'];

        exit($this->load->view('utilities/list', $view));
    }

    public function prepareTable($table = 'users', $orderby = 'users_id', $direction = 'asc', $page = 1, $record_limit = 15, $column = null, $is = null)
    {
        $paginated = $this->load->model('pagination')->paginate($table, $orderby, $direction, $page, $record_limit, $column, $is);

        $view['users'] = [];

        foreach ($paginated['records'] as $user) {
            switch ($user['group']) {
                case '1':
                    $group = 'Pre-Activation';
                break;
                case '2':
                    $group = 'Registered';
                break;
                case '3':
                    $group = 'Moderator';
                break;
                case '4':
                    $group = 'Admin';
                break;
                case '0':
                    $group = 'Locked';
                break;
                default:
                    $group = 'Activation pending';
                break;
            }
            
            $view['users'][] = [
                'users_id' => $user['users_id'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'username' => $user['username'],
                'email' => $user['email'],
                'signup_date' => date('d M, Y', strtotime($user['signup_date'])),
                'status' => $this->userOnline($user['username']) ? 'Online' : 'Offline',
                'group' => $group,
                'group_num' => $user['group']
            ];
        }

        $output = [
            'list' => $this->load->view('users/list', $view),
            'table' => $table,
            'orderby' => $orderby,
            'direction' => $direction,
            'record_limit' => $record_limit,
            'page' => $page,
            'start' => $paginated['start'],
            'total_pages' => $paginated['pages'],
            'total_records' => $paginated['total']
        ];

        return $output;
    }

    public function getTable() 
    {
        $orderby = empty($_POST['orderby']) ? null : $_POST['orderby'];
        $direction = empty($_POST['direction']) ? null : $_POST['direction'];
        $page = empty($_POST['page']) ? null : $_POST['page'];
        $record_limit = empty($_POST['record_limit']) ? null : $_POST['record_limit'];
        $column = empty($_POST['column']) ? null : $_POST['column'];
        $is = empty($_POST['is']) ? null : $_POST['is'];
        $data = $this->prepareTable('users', $orderby, $direction, $page, $record_limit, $column, $is);
        
        $output = [
            'list' => $data['list'], 
            'page' => $data['page'], 
            'start' => $data['start'],
            'total_pages' => $data['total_pages'],
            'total_records' => $data['total_records']
        ];

        $this->output->json($output, 'exit');
    }

    public function user($id)
    {       
        $user = $this->load->model('users')->getUser('users_id', $id);
        if (!$user) $this->load->route('/users/list');

        $is_online = $this->userOnline($user['users_id']);
        $la_days_ago = $this->helper->getDaysAgo($user['last_active']);
        $sd_days_ago = $this->helper->getDaysAgo($user['signup_date']);
        $today = date('c');

        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();

        if ($user['users_id'] == $this->session->id) {
            $view['self'] = true;
        } else {
            $view['self'] = null;
        }

        if ($id) {
            $view['users_id'] = $user['users_id'];
            if ($user['group'] == 1) { $view['group'] = 'Activation pending'; }
            if ($user['group'] == 2) { $view['group'] = 'Registered'; }
            if ($user['group'] == 3) { $view['group'] = 'Moderator'; }
            if ($user['group'] == 4) { $view['group'] = 'Administrator'; }
            if ($user['group'] == 0) { $view['group'] = 'Locked'; }
            $view['firstname'] = $user['firstname'];
            $view['lastname'] = $user['lastname'];
            $view['username'] = $user['username'];
            $view['email'] = $user['email'];
            $view['registered'] = date('d M, Y', strtotime($user['signup_date']));
            $view['last_active'] = $user['last_active'] ? date('d M, Y', strtotime($user['last_active'])) : date('d M, Y', strtotime($today));
            if ($user['privacy'] == 0) { $view['privacy'] = 'Public'; }
            if ($user['privacy'] == 1) { $view['privacy'] = 'Private'; }
            if ($user['privacy'] == 2) { $view['privacy'] = 'Locked'; }
            $view['avatar'] = $user['avatar'];
            $view['status'] = $is_online ? 'Online' : 'Offline';
            $view['la_days_ago'] = $la_days_ago != 0 ? $la_days_ago . ' days ago' : 'Today';
            $view['sd_days_ago'] = $sd_days_ago . ' days ago';

            exit($this->load->view('users/user', $view));
        }
    }

    private function userOnline($username) 
    {
        $user = $this->load->model('users')->getUser('username', $username);
        $last_active = strtotime($user['last_active']);

        if (time() - $last_active > 5 * 60) {
            return false;
        } else {
            return true;
        }
    }

    public function edit()
    {
        $model = $this->load->model('users');
        $group = $_POST['group'];

        foreach ($_POST['ids'] as $id) {
            $user = $model->getUser('users_id', $id['value']);

            if ($user) {
                switch ($group) {
                    case '0':
                        $group_text = 'Locked';
                        break;
                    case '1':
                        $group_text = 'Un-registered';
                        break;
                    case '2':
                        $group_text = 'Registered';
                        break;
                    case '3':
                        $group_text = 'Moderator';
                        break;
                    case '4':
                        $group_text = 'Administrator';
                        break;
                    default:
                        $group_text = 'Unknown';
                        break;
                }

                $data['group'] = $group;
                $data['users_id'] = $id['value'];

                $output['group'] = $group;

                if ($model->updateUser($data, 'users_id')) {
                    $output['alert'] = 'success';
                    $output['message'] = $this->language->get('users/user_updated');
                    $this->log('Admin "' . $this->logged_user['firstname'] . ' ' . $this->logged_user['lastname'] .  '" set user "' . $user['firstname'] . ' ' . $user['lastname'] . '" to "' . $group_text . '".');
                } else {
                    $output['alert'] = 'error';
                    $output['message'] = 'User not updated.';
                    $this->log('Admin "' . $this->logged_user['firstname'] . ' ' . $this->logged_user['lastname'] .  '" was unable to update user "' . $user['firstname'] . ' ' . $user['lastname'] . '". Check error logs.');
                }
            }
        }

        $this->output->json($output, 'exit');
    }

    public function delete()
    {
        $model = $this->load->model('users');
        $admin = $model->getUser('users_id', $this->session->id);

        foreach ($_POST as $id) {
            $user = $model->getUser('users_id', $id);

            if ($user && $user['group'] != 4) {
                if ($model->deleteUser($id)) {
                    $output = ['alert' => 'success', 'message' => $this->language->get('users/users_deleted')];
                    $this->log('Admin "' . $this->logged_user['username'] . '" deleted user "' . $user['username'] . '".');
                } else {
                    $output = ['alert' => 'error', 'message' => 'User delete failed.'];
                    $this->log('Admin "' . $this->logged_user['username'] . '" was unable to delete user "' . $user['username'] . '". Check error logs.');
                }
            }   
        }

        $this->output->json($output);
    }
}