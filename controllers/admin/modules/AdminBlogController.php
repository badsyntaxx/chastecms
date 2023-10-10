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
    public function init($view = null, $edit = false)
    {
        if ($view) {
            if ($view === 'new') {
                $this->edit();
            }
            if ($edit) {
                $this->drawBlogEditor($view);
            }
        }

        exit($this->load->controller('admin/pagination')->drawPagination('modules/blog'));
    }

    public function setPaginationParams()
    {
        $params = ['table' => 'pages', 'orderby' => 'blog_id', 'direction' => 'asc', 'page' => 1, 'limit' => 15];
        $this->output->json($params);
    }

    public function drawTable() 
    {
        $paginated = $this->load->model('pagination')->paginate('blog', 'blog_id', 'asc', 1, 15);
        $posts = $paginated['records'];
        $view['posts'] = [];

        foreach ($posts as $post) {
            $views = $post['views'] ? $post['views'] : 0;
            $last_view = $post['last_view'] ? date('d M, Y g:ia', strtotime($post['last_view'])) : 'Never';
            $last_edit = $post['last_edit'] ? date('d M, Y g:ia', strtotime($post['last_edit'])) : 'Never';
            $p_days_ago = getDaysAgo($post['post_date']);
            $le_days_ago = getDaysAgo($post['last_edit']);
            $lv_days_ago = getDaysAgo($post['last_view']);

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

        $output = ['table' => $this->load->view('modules/blog/list', $view), 'start' => $paginated['start']];
        $this->output->json($output, 'exit');
    }

    public function drawBlogEditor($id)
    {
        $post = $this->load->model('blog')->getPost('blog_id', $id);

        $view['title'] = 'Blog Editor';
        $view['logged'] = $this->logged_user;
        $view['theme'] = $this->load->model('settings')->getSetting('theme');
        $view['blog_id'] = $post['blog_id'];
        $view['title'] = $post['title'];
        $view['body'] = $post['body'];

        exit($this->load->view('modules/blog/edit', $view));
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
                $this->gusto->log('Admin "' . $this->logged_user['username'] . '" created a new blog post titled, "' . $data['title'] . '".');
                $output = ['alert' => 'success', 'message' => $this->language->get('modules/blog/post_saved')];
            } else {
                $output = ['alert' => 'error', 'message' => 'Post not savead'];
            }

            $this->output->json($output, 'exit');
        }
    }

    public function edit($id = null)
    {
        $view['theme'] = $this->load->model('settings')->getSetting('theme');
        $view['title'] = 'Blog Editor';
        $view['logged'] = $this->logged_user;

        if ($id) {
            $post = $this->load->model('blog')->getPost('blog_id', $id);

            $view['title'] = $post['title'];
            $view['blog_id'] = $id;
            $view['title'] = $post['title'];
            $view['body'] = $post['body'];
            
            exit($this->load->view('blog', $view)); 
        } else {
            exit($this->load->view('modules/blog/new', $view)); 
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
            $this->gusto->log('Admin "' . $this->logged_user['username'] . '" updated the blog post titled, "' . $data['title'] . '".');
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
                    $this->gusto->log('Admin "' . $this->logged_user['username'] . '" deleted a blog post titled, "' . $post['title'] . '".');
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
            $this->gusto->log('Admin "' . $this->logged_user['username'] . '" uploaded images "' . $_FILES['upload_image']['name'] . '" for the blog.');
            exit($this->language->get('pages/file_uploaded')); 
        }
    }
}