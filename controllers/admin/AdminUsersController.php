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
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/admin/users
     * - http://root/admin/users/init
     *
     * This method will load the users list table.
     */
    public function init($view = 'list')
    {
        if ($view == 'list') {
            exit($this->load->controller('admin/pagination')->drawPagination('users'));
        }

        $this->drawUser($view);
    }

    public function setPaginationParams()
    {
        $params = ['table' => 'users', 'orderby' => 'users_id', 'direction' => 'asc', 'page' => 1, 'limit' => 15];
        $this->output->json($params);
    }

    public function drawTable()
    {
        $paginated = $this->load->model('pagination')->paginate('users', $_POST['orderby'], $_POST['direction'], $_POST['page'], $_POST['limit']);

        foreach ($paginated['records'] as $user) {
            switch ($user['group']) {
                case '2':
                    $group = 'Registered';
                    break;
                case '3':
                    $group = 'Moderator';
                    break;
                case '4':
                    $group = 'Admin';
                    break;
                case '66':
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
            'table' => $this->load->view('users/list', $view), 
            'start' => $paginated['start']
        ];

        $this->output->json($output, 'exit');
    }

    private function drawUser($username)
    {
        $user = $this->load->model('users')->getUser('username', $username);
        if (!$user) $this->load->route('/admin/users/list');

        $is_online = $this->userOnline($user['username']);
        $la_days_ago = getDaysAgo($user['last_active']);
        $sd_days_ago = getDaysAgo($user['signup_date']);
        $today = date('c');
        $settings_model = $this->load->model('settings');

        $view['header'] = $this->load->controller('admin/header')->init();
        $view['footer'] = $this->load->controller('admin/footer')->init();
        $view['search'] = $this->load->controller('admin/search')->init();
        $view['nav'] = $this->load->controller('admin/navigation')->init();
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->init();

        if ($user['users_id'] == $this->session->id) {
            $view['self'] = true;
        } else {
            $view['self'] = null;
        }

        if ($username) {
            $view['users_id'] = $user['users_id'];
            if ($user['group'] == 1) { $view['group'] = 'Activation pending'; }
            if ($user['group'] == 2) { $view['group'] = 'Registered'; }
            if ($user['group'] == 3) { $view['group'] = 'Moderator'; }
            if ($user['group'] == 4) { $view['group'] = 'Administrator'; }
            if ($user['group'] == 66) { $view['group'] = 'Locked'; }
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
        $admin = $model->getUser('users_id', $this->logged_user['users_id']);
        $group = $_POST['group'];

        foreach ($_POST['ids'] as $id) {
            $user = $model->getUser('users_id', $id['value']);

            if ($user) {
                switch ($group) {
                    case '66':
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
                    if ($group != '66') {
                        $model->deleteLoginAttempts($user['email']);
                    }
                    $output['alert'] = 'success';
                    $output['message'] = $this->language->get('users/user_updated');
                    $this->gusto->log('Admin "' . $this->logged_user['username'] . '" set user "' . $user['username'] . '" to "' . $group_text . '".');
                } else {
                    $output['alert'] = 'error';
                    $output['message'] = 'User not updated.';
                    $this->gusto->log('Admin "' . $this->logged_user['username'] . '" was unable to update user "' . $user['username'] . '". Check error logs.');
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
                    $this->gusto->log('Admin "' . $this->logged_user['username'] . '" deleted user "' . $user['username'] . '".');
                } else {
                    $output = ['alert' => 'error', 'message' => 'User delete failed.'];
                    $this->gusto->log('Admin "' . $this->logged_user['username'] . '" was unable to delete user "' . $user['username'] . '". Check error logs.');
                }
            }   
        }

        $this->output->json($output);
    }
}