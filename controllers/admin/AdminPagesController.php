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
    public function index($new = null)
    {
        if ($new == 'new') {
            $this->drawPageMaker();
        }

        $data = $this->prepareTable();

        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();
        $view['controls'] = $this->load->view('pages/controls');
        $view['filters'] = $this->prepareFilters();
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

    public function prepareTable($table = 'pages', $orderby = 'pages_id', $direction = 'asc', $page = 1, $record_limit = 15, $column = null, $is = null)
    {
        $paginated = $this->load->model('pagination')->paginate($table, $orderby, $direction, $page, $record_limit, $column, $is);

        $view['pages_list'] = [];

        foreach ($paginated['records'] as $p) {
            $views = $p['views'] ? $p['views'] : 0;
            $locked = $p['locked'] == 0 ? null : true;
            $route = $this->load->model('pages')->getRoute('routes_id', $p['pages_id']);

            $view['pages_list'][] = [
                'pages_id' => $p['pages_id'],
                'name' => $p['name'],
                'title' => $p['title'],
                'route' => $route['route'],
                'content' => $p['content'],
                'views' => $views,
                'link' => $p['name'],
                'locked' => $locked
            ];
        }

        $view['url'] = HOST;

        $output = [
            'list' => $this->load->view('pages/list', $view),
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
        $data = $this->prepareTable('pages', $orderby, $direction, $page, $record_limit, $column, $is);
        
        $output = [
            'list' => $data['list'], 
            'page' => $data['page'], 
            'start' => $data['start'],
            'total_pages' => $data['total_pages'],
            'total_records' => $data['total_records']
        ];

        $this->output->json($output, 'exit');
    }

    private function prepareFilters() 
    {
        $names = $this->load->model('pages')->getSpecificPageData('name');
        $views = $this->load->model('pages')->getSpecificPageData('views');

        if ($names) {
            $view['names'] = array_unique($names);
            sort($view['names']);
        }

        if ($views) {
            $view['views'] = array_unique($views);
            sort($view['views']);
        }

        return $this->load->view('pages/filters', $view);
    }

    public function page($page_name)
    {
        if (!$page_name) {
            $this->load->route('/admin/pages/');
        }

        $pages_model = $this->load->model('pages');
        $nav_model = $this->load->model('navigation');
        $page = $pages_model->getPage('name', $page_name);
        $route = $pages_model->getRoute('route_anchor', $page['pages_id']);
        $link = $nav_model->getNavLink('nav_anchor', $page['pages_id']);
        $links = $nav_model->getTopNavLinks($route['route_anchor']);
        $c_days_ago = $this->helper->getDaysAgo($page['creation_date']);
        $le_days_ago = $this->helper->getDaysAgo($page['last_edit']);
        $lv_days_ago = $this->helper->getDaysAgo($page['last_view']);

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

        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();
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
        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();

        exit($this->load->view('pages/new', $view));
    }

    public function edit($page_name)
    {
        $pages_model = $this->load->model('pages');
        $page = $pages_model->getPage('name', $page_name);
        $route = $pages_model->getRoute('route_anchor', $page['pages_id']);

        $view['title'] = 'Page Editor';
        $view['logged'] = $this->logged_user;
        $view['theme'] = $this->load->model('settings')->getSetting('theme');
        $view['pages_id'] = $page['pages_id'];
        $view['name'] = $page['name'];
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

            $this->log('Admin "' . $this->logged_user['username'] . '" created a new page, "' . $post['name'] . '".');
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
            fwrite($controller, '    public function index()' . PHP_EOL);
            fwrite($controller, '    {' . PHP_EOL);
            fwrite($controller, '        $page = $this->load->model(\'pages\')->getPage(\'name\', \'' . $name . '\');' . PHP_EOL);
            fwrite($controller, ' ' . PHP_EOL);
            fwrite($controller, '        $data[\'title\'] = $page[\'title\'];' . PHP_EOL);
            fwrite($controller, '        $data[\'description\'] = $page[\'description\'];' . PHP_EOL);
            fwrite($controller, ' ' . PHP_EOL);
            fwrite($controller, '        $view[\'header\'] = $this->load->controller(\'header\')->index($data);' . PHP_EOL);
            fwrite($controller, '        $view[\'footer\'] = $this->load->controller(\'footer\')->index();' . PHP_EOL);
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
        $data['nav_name'] = $post['nav_name'];
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

        $nav_data['nav_name'] = $_POST['nav_name'];
        $nav_data['top'] = isset($_POST['top']) ? $_POST['top'] : 0;
        $nav_data['bottom'] = isset($_POST['bottom']) ? $_POST['bottom'] : 0;
        $nav_data['parent'] = !empty($_POST['parent']) ? $_POST['parent'] : 0;
        $nav_data['nav_anchor'] = $_POST['pages_id'];
        $nav_data['sort_order'] = !empty($_POST['sort_order']) ? $_POST['sort_order'] : 0;

        $this->load->model('navigation')->updateNavLink('nav_anchor', $nav_data);
        $this->updateChildren();

        $this->log('Admin "' . $this->logged_user['username'] . '" updated information for "' . $_POST['name'] . '" page.');
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

    public function updateEdit()
    {
        $data['pages_id'] = $_POST['pages_id'];
        $data['last_edit'] = date('c');
        $data['content'] = $_POST['content'];

        $pages_model = $this->load->model('pages');
        $page = $pages_model->getPage('pages_id', $data['pages_id']);
        $pages_model->updatePage($data);

        $this->log('Admin "' . $this->logged_user['username'] . '" made design edits to "' . $page['name'] . '" page.');
        $output = ['alert' => 'success', 'message' => $this->language->get('pages/page_updated')];
        $this->output->json($output, 'exit');
    }

    public function upload()
    {
        $upload_dir = $_POST['upload_dir'];
        $upload_library = $this->load->library('Upload');
        
        $upload_library->uploadImage($_FILES['upload_image'], $upload_dir);

        if ($upload_library->file_invalid) {
            $output = ['alert' => 'error', 'message' => $this->language->get('upload/file_invalid')];
            $this->output->json($output, 'exit');
        }
        if ($upload_library->file_big) {
            $search = ['{{filesize}}', '{{maxFilesize}}'];
            $replace = [$upload_library->file_big['size'], $upload_library->file_big['max']];
            $output = ['alert' => 'error', 'message' => str_replace($search, $replace, $this->language->get('upload/file_big'))];
            $this->output->json($output, 'exit');
        }

        if ($upload_library->upload_success) {
            $this->log('Admin "' . $this->logged_user['username'] . '" uploaded images "' . $_FILES['upload_image']['name'] . '".');
            $output = ['alert' => 'success', 'message' => $this->language->get('upload/success')];
            $this->output->json($output, 'exit');
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
            $this->log('Admin "' . $this->logged_user['username'] . '" deleted the "' . $page['name'] . '" page.');
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