<?php 

/**
 * Admin Pages Controller Class
 *
 *
 * 
 */
class AdminPagesController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/admin/pages
     * - http://root/admin/pages/init
     *
     * This method loads the pages list table, the new page maker and the page editor.
     */
    public function init($view = null, $edit = false)
    {
        if ($view) {
            if ($view == 'new') {
                $this->drawPageMaker();
            }
            if ($edit) {
                $this->drawPageEditor($view);
            }
            $this->drawPage($view);
        }

        exit($this->load->controller('admin/pagination')->drawPagination('pages'));
    }

    public function setPaginationParams()
    {
        $params = ['table' => 'pages', 'orderby' => 'pages_id', 'direction' => 'asc', 'page' => 1, 'limit' => 15];
        $this->output->json($params);
    }

    public function drawTable() 
    {
        $paginated = $this->load->model('pagination')->paginate('pages', $_POST['orderby'], $_POST['direction'], $_POST['page'], $_POST['limit']);

        foreach ($paginated['records'] as $page) {
            $views = $page['views'] ? $page['views'] : 0;
            $locked = $page['locked'] == 0 ? null : true;
            $route = $this->load->model('pages')->getRoute('routes_id', $page['pages_id']);

            $view['pages_list'][] = [
                'pages_id' => $page['pages_id'],
                'name' => $page['name'],
                'title' => $page['title'],
                'route' => $route['route'],
                'content' => $page['content'],
                'views' => $views,
                'link' => $page['name'],
                'locked' => $locked
            ];
        }

        $view['url'] = HOST;

        $output = ['table' => $this->load->view('pages/list', $view), 'start' => $paginated['start']];
        $this->output->json($output, 'exit');
    }

    public function drawPage($page_name)
    {
        if (!$page_name) {
            $this->load->route('/admin/pages/');
        }

        $pages_model = $this->load->model('pages');
        $nav_model = $this->load->model('navigation');
        $page = $pages_model->getPage('name', $page_name);
        $route = $pages_model->getRoute('route_anchor', $page['pages_id']);
        $link = $nav_model->getNavLink('nav_anchor', $page['pages_id']);
        $links = $nav_model->getTopNavLinks($route['route']);
        $c_days_ago = getDaysAgo($page['creation_date']);
        $le_days_ago = getDaysAgo($page['last_edit']);
        $lv_days_ago = getDaysAgo($page['last_view']);

        switch ($page_name) {
            case 'signup':
                $view['edit_lock'] = true;
                break;
            case 'login':
                $view['edit_lock'] = true;
                break;
            default:
                $view['edit_lock'] = false;
                break;
        }

        $view['header'] = $this->load->controller('admin/header')->init();
        $view['footer'] = $this->load->controller('admin/footer')->init();
        $view['search'] = $this->load->controller('admin/search')->init();
        $view['nav'] = $this->load->controller('admin/navigation')->init();
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->init();
        $view['content'] = $page['content'];
        $view['title'] = $page['title'];
        $view['description'] = $page['description'];
        $view['pages_id'] = $page['pages_id'];
        $view['name'] = $page['name'];
        $view['route'] = $route['route'];
        $view['nav_name'] = $nav_model->getNavLinkName('nav_anchor', $page['pages_id']);
        $view['links'] = $links;
        $view['sort_order'] = $link['sort_order'];
        $view['created'] = date('d M, Y g:ia', strtotime($page['creation_date']));
        $view['c_days_ago'] = $c_days_ago === 0 ? 'Today' : $c_days_ago . ' days ago';
        $view['last_edit'] = isset($page['last_edit']) ? date('d M, Y g:ia', strtotime($page['last_edit'])) : 'Never';
        $view['le_days_ago'] = $le_days_ago === 0 ? 'Today' : $le_days_ago . ' days ago';
        $view['views'] = $page['views'];
        $view['last_view'] = isset($page['last_view']) ? date('d M, Y g:ia', strtotime($page['last_view'])) : 'Never';
        $view['lv_days_ago'] = $lv_days_ago === 0 ? 'Today' : $lv_days_ago . ' days ago';

        exit($this->load->view('pages/page', $view));
    }

    public function getNavlinkData()
    {
        $model = $this->load->model('navigation');
        $link = $model->getNavLink('nav_anchor', $_POST['pages_id']);
        if ($link) {
            $parent = $model->getNavLink('navigation_id', $link['parent']);
        }

        $output['parent'] = $parent['navigation_id'];
        $output['top'] = $link['top'];
        $output['bottom'] = $link['bottom'];

        $this->output->json($output, 'exit');
    }

    private function drawPageMaker()
    {
        $view['header'] = $this->load->controller('admin/header')->init();
        $view['footer'] = $this->load->controller('admin/footer')->init();
        $view['search'] = $this->load->controller('admin/search')->init();
        $view['nav'] = $this->load->controller('admin/navigation')->init();
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->init();

        exit($this->load->view('pages/new', $view));
    }

    private function drawPageEditor($page_name)
    {
        $pages_model = $this->load->model('pages');
        $page = $pages_model->getPage('name', $page_name);
        $route = $pages_model->getRoute('route_anchor', $page['pages_id']);

        $view['title'] = 'Page Editor';
        $view['logged'] = $this->logged_user;
        $view['theme'] = $this->load->model('settings')->getSetting('theme');
        $view['pages_id'] = $page['pages_id'];
        $view['name'] = ucfirst($page['name']);
        $view['route'] = $route['route'];
        $view['content'] = $page['content'];

        exit($this->load->view('pages/edit', $view));
    }

    public function insertPageData()
    {
        $pages_model = $this->load->model('pages');

        $post['title'] = trim($_POST['page_title']);
        $post['name'] = trim($_POST['page_name']);
        $post['route'] = trim($_POST['page_route']);
        $post['controller_file'] = trim($_POST['page_controller']);
        $post['nav_name'] = trim($_POST['nav_name']);
        $post['creation_date'] = date('c');

        if (!empty($post['title'])) {
            $page = $pages_model->getPage('name', $post['name']);
            $route = $pages_model->getRoute('route', $post['route']);

            if ($page) {
                $output = ['alert' => 'error', 'message' => $this->language->get('pages/page_exists')];
                $this->output->json($output, 'exit');
            }

            if ($route) {
                $output = ['alert' => 'error', 'message' => $this->language->get('pages/route_exists')];
                $this->output->json($output, 'exit');
            }

            if ($pages_model->insertPage($post)) {
                $this->createPageController($post['name'], $post['controller_file']);
                $this->insertRoute($post);
                $this->insertNavData($post);
            } else {
                $output = ['alert' => 'error', 'message' => $this->language->get('pages/data_fail')];
                $this->output->json($output, 'exit');
            }

            $this->gusto->log('Admin "' . $this->logged_user['username'] . '" created a new page, "' . $post['name'] . '".');
            $output = ['alert' => 'success', 'message' => $this->language->get('pages/page_created')];
            $this->output->json($output, 'exit');
        }

        $output = ['alert' => 'warning', 'message' => $this->language->get('pages/no_name')];
        $this->output->json($output, 'exit');
    }

    private function createPageController($name, $controller)
    {
        $controller_name = str_replace('.php', '', $controller);
        $controller = fopen(ROOT_DIR . '/controllers/front/content/' . $controller, 'w+') or exit('Unable to open the controller file! "' . $controller . '"');

        if (fwrite($controller, '<?php ' . PHP_EOL)) {
            fwrite($controller, ' ' . PHP_EOL);
            fwrite($controller, '/**' . PHP_EOL);
            fwrite($controller, ' * ' . $controller_name . ' Controller Class' . PHP_EOL);
            fwrite($controller, ' */' . PHP_EOL);
            fwrite($controller, 'class ' . $controller_name . ' extends Controller' . PHP_EOL);
            fwrite($controller, '{' . PHP_EOL);
            fwrite($controller, '    /**' . PHP_EOL);
            fwrite($controller, '     * Init method' . PHP_EOL);
            fwrite($controller, '     *' . PHP_EOL);
            fwrite($controller, '     * The init method is the default for controller classes. Whenever a controller' . PHP_EOL);
            fwrite($controller, '     * class is instantiated the init method will be called.' . PHP_EOL);
            fwrite($controller, '     */' . PHP_EOL);
            fwrite($controller, '    public function init()' . PHP_EOL);
            fwrite($controller, '    {' . PHP_EOL);
            fwrite($controller, '        $page = $this->load->model(\'pages\')->getPage(\'name\', \'' . $name . '\');' . PHP_EOL);
            fwrite($controller, ' ' . PHP_EOL);
            fwrite($controller, '        $data[\'title\'] = $page[\'title\'];' . PHP_EOL);
            fwrite($controller, '        $data[\'description\'] = $page[\'description\'];' . PHP_EOL);
            fwrite($controller, ' ' . PHP_EOL);
            fwrite($controller, '        $view[\'header\'] = $this->load->controller(\'header\')->init($data);' . PHP_EOL);
            fwrite($controller, '        $view[\'footer\'] = $this->load->controller(\'footer\')->init();' . PHP_EOL);
            fwrite($controller, '        $view[\'content\'] = $this->load->model(\'pages\')->getPageContent(\'' . $name . '\');' . PHP_EOL);
            fwrite($controller, ' ' . PHP_EOL);
            fwrite($controller, '        $this->load->model(\'pages\')->updatePageStatistics(\'' . $name . '\');' . PHP_EOL);
            fwrite($controller, ' ' . PHP_EOL);
            fwrite($controller, '        exit($this->load->view(\'common/content\', $view));' . PHP_EOL);
            fwrite($controller, '    }' . PHP_EOL);
            fwrite($controller, '}');
            fclose($controller);
        } else {
            $output = ['alert' => 'error', 'message' => $this->language->get('pages/file_fail')];
            $this->output->json($output, 'exit');
        }
    }

    private function insertRoute($post)
    {
        $model = $this->load->model('pages');
        $page = $model->getPage('name', $post['name']);
        $route = $model->getRoute('route', $post['route']);

        if (!$route) {
            $data['route_anchor'] = $page['pages_id'];
            $data['route'] = $post['route'];
            $data['controller'] = str_replace('.php', '', $post['controller_file']);

            if (!$model->insertRoute($data)) {
                $output = ['alert' => 'error', 'message' => $this->language->get('pages/route_fail')];
                $this->output->json($output, 'exit');
            }
        }
    }

    private function insertNavData($post)
    {
        $page = $this->load->model('pages')->getPage('name', $post['name']);
        $model = $this->load->model('navigation');
        $links = $model->getNavLinks();
        $sort_order = 1;

        if ($links) {
            $sort_order += $model->getHighestSortNumber();
        }
        
        $data['nav_anchor'] = $page['pages_id'];
        $data['name'] = $post['nav_name'];
        $data['route'] = $post['route'];
        $data['sort_order'] = $sort_order;
        $data['parent'] = 0;
        $data['children'] = 0;
        $data['top'] = 1;
        $data['bottom'] = 0;

        if (!$model->insertNavData($data)) {
            $output = ['alert' => 'error', 'message' => $this->language->get('pages/nav_fail')];
            $this->output->json($output, 'exit');
        }
    }

    public function update()
    {
        $pages_model = $this->load->model('pages');

        $page_data['title'] = $_POST['title'];
        $page_data['description'] = $_POST['description'];
        $page_data['pages_id'] = $_POST['pages_id'];
        $page_data['last_edit'] = date('c');
        $pages_model->updatePage($page_data);

        if (!empty($_POST['route'])) {
            $route = $pages_model->getRoute('route', $_POST['route']);

            if ($route) {
                if ($route['route_anchor'] != $_POST['pages_id']) {
                    $output = ['alert' => 'error', 'message' => $this->language->get('pages/route_exists')];
                    $this->output->json($output, 'exit');
                }                
            }

            $route = trim($_POST['route']);
            $route = '/' . ltrim($route, '/');
            $route = rtrim($route, '/');

            $route_data['route'] = $route;
            $route_data['route_anchor'] = $_POST['pages_id'];
            $pages_model->updateRoute('route_anchor', $route_data);
        }

        $nav_data['route'] = $_POST['route'];
        $nav_data['name'] = $_POST['nav_name'];
        $nav_data['top'] = isset($_POST['top']) ? $_POST['top'] : 0;
        $nav_data['bottom'] = isset($_POST['bottom']) ? $_POST['bottom'] : 0;
        $nav_data['parent'] = !empty($_POST['parent']) ? $_POST['parent'] : 0;
        $nav_data['nav_anchor'] = $_POST['pages_id'];
        $nav_data['sort_order'] = !empty($_POST['sort_order']) ? $_POST['sort_order'] : 0;

        $this->load->model('navigation')->updateNavLink('nav_anchor', $nav_data);
        $this->updateChildren();

        $this->gusto->log('Admin "' . $this->logged_user['username'] . '" updated information for "' . $_POST['name'] . '" page.');
        $output = ['alert' => 'success', 'message' => $this->language->get('pages/page_updated')];
        $this->output->json($output, 'exit');
    }

    private function updateChildren()
    {
        $model = $this->load->model('navigation');
        $links = $model->getNavLinks();
        $parents = [];

        // Set all nav links children to 0
        foreach ($links as $link) {
            $data['navigation_id'] = $link['navigation_id'];
            $data['children'] = 0;
            $model->updateNavLink('navigation_id', $data);
        }

        // Create an array of parents
        foreach ($links as $link) {
            if ($link['parent'] != 0) {
                array_push($parents, $link['parent']);
            }
        }

        foreach ($parents as $parent) {
            $data['navigation_id'] = $parent;
            $data['children'] = 1;
            $model->updateNavLink('navigation_id', $data);
        }
    }

    public function edit()
    {
        $data['pages_id'] = $_POST['pages_id'];
        $data['last_edit'] = date('c');
        $data['content'] = $_POST['content'];

        $pages_model = $this->load->model('pages');
        $page = $pages_model->getPage('pages_id', $data['pages_id']);
        $pages_model->updatePage($data);

        $this->gusto->log('Admin "' . $this->logged_user['username'] . '" made design edits to "' . $page['name'] . '" page.');
        $output = ['alert' => 'success', 'message' => $this->language->get('pages/page_updated')];
        $this->output->json($output, 'exit');
    }

    public function upload()
    {
        $user = $this->load->model('users')->getUser('users_id', $this->session->id);
        $upload_dir = $_POST['upload_dir'];
        $upload_library = $this->load->library('Upload');
        
        $upload_library->uploadImage($_FILES['upload_image'], $upload_dir);

        if ($upload_library->file_invalid) {
            exit($this->language->get('account/file_invalid'));
        }
        if ($upload_library->filebig) {
            exit($this->language->get('account/file_big'));
        }

        if ($upload_library->upload_success) {
            $this->gusto->log('Admin "' . $this->logged_user['username'] . '" uploaded images "' . $_FILES['upload_image']['name'] . '".');
            exit($this->language->get('pages/file_uploaded')); 
        }
    }

    public function delete()
    {
        $page_model = $this->load->model('pages');
        $controllers_dir = ROOT_DIR . '/controllers/front/content/';

        foreach ($_POST as $id) {
            $page = $this->load->model('pages')->getPage('pages_id', $id);
            if ($page) {
                if (is_file($controllers_dir . $page['controller_file'])) {
                    if (!unlink($controllers_dir . $page['controller_file'])) {
                        exit('Unable to delete controller file.');
                    }
                } 

                if (!$page_model->deletePage($id)) {
                    exit('Unable to delete page record.');
                }
            }      

            $this->deleteRoute($id);
            $this->deleteNavLink($id);
            $this->updateChildren();
            $this->gusto->log('Admin "' . $this->logged_user['username'] . '" deleted the "' . $page['name'] . '" page.');
        }

        $output = ['alert' => 'success', 'message' => $this->language->get('pages/pages_deleted')];
        $this->output->json($output);
    }

    private function deleteRoute($id)
    {
        $model = $this->load->model('pages');
        $routes = $model->getRoutes('route_anchor', $id);

        foreach ($routes as $route) {
            if (!$model->deleteRoute('route_anchor', $route['route_anchor'])) {
                exit('Unable to delete route.');
            }
        }
    }

    private function deleteNavLink($id)
    {
        $model = $this->load->model('navigation');
        $link = $model->getNavLink('nav_anchor', $id);

        if ($link) {
            if (!$model->deleteNavLink($id)) {
                exit('Unable to delete nav link.');
            }
        }
    }

    public function resetPageStats()
    {
        $pages_model = $this->load->model('pages');
        $pages = $pages_model->getPages();

        foreach ($pages as $p) {
            $data['views'] = 0;
            $data['last_view'] = null;
            $data['last_edit'] = null;
            $data['pages_id'] = $p['pages_id'];
            $pages_model->updatePage($data);
        }

        exit('Page stats reset.');
    }
}