<?php 

/**
 * Admin Blog Controller Class
 *
 * This class gets blog data from the database and sets variables for 
 * display in the view.
 */
class AdminBlogController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/admin/blog
     * - http://root/admin/blog/init
     *
     * This method will access other methods in the class to do a few tasks 
     * related to the sitemap and display the sitemap view.
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
        $view['controls'] = $this->load->view('blog/controls');
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

    public function prepareTable($table = 'blog', $orderby = 'blog_id', $direction = 'asc', $page = 1, $record_limit = 15, $column = null, $is = null)
    {
        $paginated = $this->load->model('pagination')->paginate($table, $orderby, $direction, $page, $record_limit, $column, $is);

        $view['posts'] = [];

        foreach ($paginated['records'] as $post) {
            $views = $post['views'] ? $post['views'] : 0;
            $last_view = $post['last_view'] ? date('d M, Y g:ia', strtotime($post['last_view'])) : 'Never';
            $last_edit = $post['last_edit'] ? date('d M, Y g:ia', strtotime($post['last_edit'])) : 'Never';
            $p_days_ago = $this->helper->getDaysAgo($post['post_date']);
            $le_days_ago = $this->helper->getDaysAgo($post['last_edit']);
            $lv_days_ago = $this->helper->getDaysAgo($post['last_view']);

            $view['posts'][] = [
                'blog_id' => $post['blog_id'],
                'author' => $post['author'],
                'title' => $post['title'],
                'body' => $post['body'],
                'views' => $views,
                'last_view' => $last_view,
                'lv_days_ago' => $lv_days_ago === 0 ? 'Today' : $lv_days_ago . ' days ago',
                'last_edit' => $last_edit,
                'le_days_ago' => $le_days_ago === 0 ? 'Today' : $le_days_ago . ' days ago',
                'posted' => date('d M, Y g:ia', strtotime($post['post_date'])),
                'p_days_ago' => $p_days_ago === 0 ? 'Today' : $p_days_ago . ' days ago'
            ];
        }

        $output = [
            'list' => $this->load->view('/blog/list', $view),
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
        $data = $this->prepareTable('blog', $orderby, $direction, $page, $record_limit, $column, $is);
        
        $output = [
            'list' => $data['list'], 
            'page' => $data['page'], 
            'start' => $data['start'],
            'total_pages' => $data['total_pages'],
            'total_records' => $data['total_records']
        ];

        $this->output->json($output, 'exit');
    }

    public function post($blog_view = null, $id = null)
    {
        $view['theme'] = $this->load->model('settings')->getSetting('theme');
        $view['title'] = 'Blog Editor';
        $view['logged'] = $this->logged_user;

        if ($blog_view == 'edit') {
            if ($id) {
                $post = $this->load->model('blog')->getPost('blog_id', $id);

                $view['title'] = $post['title'];
                $view['blog_id'] = $id;
                $view['title'] = $post['title'];
                $view['body'] = $post['body'];
                
                exit($this->load->view('blog/edit', $view));
            }
        }

        if ($blog_view == 'new') {
            exit($this->load->view('blog/new', $view));
        }
    }

    public function save()
    {          
        if (isset($_POST['body'])) {

            $model = $this->load->model('blog');
            $data['author'] = $this->logged_user['users_id'];
            $data['title'] = 'Blog Title';
            $data['body'] = trim($_POST['body']);
            $data['post_date'] = date('c');

            if (preg_match('/<h1[^>]+>(.*?)<\/h1>/', $data['body'], $title_matches) == true || preg_match('/<h1>(.*?)<\/h1>/', $data['body'], $title_matches) == true) {
                $data['title'] = $title_matches[1]; 
            }

            if (preg_match('/(<img[^>]+>)/i', $data['body'], $image_matches) == true) {
                if (isset($image_matches[0])) {
                    $data['preview_image'] = $image_matches[0];
                }
            }

            if ($model->insertPost($data)) {
                $this->log('Admin "' . $this->logged_user['username'] . '" created a new blog post titled, "' . $data['title'] . '".');
                $output = ['alert' => 'success', 'message' => $this->language->get('modules/blog/post_saved')];
            } else {
                $output = ['alert' => 'error', 'message' => 'Post not savead'];
            }

            $this->output->json($output, 'exit');
        }
    }

    public function update()
    {
        $data['blog_id'] = $_POST['blog_id'];
        $data['title'] = 'Blog Title';
        $data['body'] = trim($_POST['body']);
        $data['preview_image'] = null;
        $data['last_edit'] = date('c');

        if (preg_match('/<h1[^>]+>(.*?)<\/h1>/', $data['body'], $title_matches) == true || preg_match('/<h1>(.*?)<\/h1>/', $data['body'], $title_matches) == true) {
            $data['title'] = $title_matches[1]; 
        }

        if (preg_match('/(<img[^>]+>)/i', $data['body'], $image_matches) == true) {
            if (isset($image_matches[0])) {
                $data['preview_image'] = $image_matches[0];
            }
        }

        if ($this->load->model('blog')->updateBlog($data)) {
            $this->log('Admin "' . $this->logged_user['username'] . '" updated the blog post titled, "' . $data['title'] . '".');
            $output = ['alert' => 'success', 'message' => $this->language->get('modules/blog/post_updated')];
        } else {
            $output = ['alert' => 'error', 'message' => 'Post not updated.'];
        }

        $this->output->json($output, 'exit');
    }

    public function delete()
    {
        $model = $this->load->model('blog');

        foreach ($_POST as $id) {
            if ($model->getPost('blog_id', $id)) {
                $post = $model->getPost('blog_id', $id);
                if ($model->deletePost($id)) {
                    $this->log('Admin "' . $this->logged_user['username'] . '" deleted a blog post titled, "' . $post['title'] . '".');
                    $output = ['alert' => 'success', 'message' => $this->language->get('modules/blog/posts_deleted')];
                } else {
                    $output = ['alert' => 'error', 'message' => $this->language->get('modules/blog/posts_deleted')];
                }
            }      
        }

        $this->output->json($output);
    }

    public function upload()
    {
        $user = $this->load->model('users')->getUser('key', $this->session->id);
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
            $this->log('Admin "' . $this->logged_user['username'] . '" uploaded images "' . $_FILES['upload_image']['name'] . '" for the blog.');
            $output = ['alert' => 'success', 'message' => $this->language->get('upload/success')];
            $this->output->json($output, 'exit'); 
        }
    }
}