<?php 

/**
 * Blog Controller Class
 *
 * This class gets blog data from the database and prepares it for the view.
 */
class BlogController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/blog
     * - http://root/blog/init
     *
     * This init method uses the link id and link title parameters to get blog
     * data from the database. If the link title is set, the view will show the 
     * post with that title. If the link title is not set, the view will 
     * display the last 5 posts.
     * 
     * @param int $id   
     * @param string $title
     */
    public function init($id = null, $title = null)
    {           
        $model = $this->load->model('blog');
        $page = $this->load->model('pages')->getPage('name', 'blog');

        $data['title'] = $page['title'];
        $data['description'] = $page['description'];

        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();

        $this->load->model('pages')->updatePageStatistics('blog');

        if ($id) {
            $post = $model->getPost('blog_id', $id);

            $view['author'] = $post['username'];
            $view['post_date'] = date('l, j M, Y', strtotime($post['post_date']));
            $view['body'] = $post['body'];

            $this->load->model('blog')->updateBlogStatistics($id);

            exit($this->load->view('blog/post', $view));
        }
        
        exit($this->load->view('blog/list', $view));
    }

    public function getBlogPosts()
    {
        $model = $this->load->model('blog');
        $limit = $_POST['limit'];
        $posts = $model->getPosts($limit);
        // $body = preg_replace('/<img[^>]+\>/i', '', $post['body']);
        // $body = preg_replace('/<p[^>]*>[\s|&nbsp;]*<\/p>/', '', $body);

        foreach ($posts as $p) {
            $view[] = [
                'blog_id' => $p['blog_id'],
                'author' => $p['username'],
                'title' => $p['title'],
                'body' => $p['body'],
                'preview_image' => $p['preview_image'],
                'post_date' => date('l, j M, Y', strtotime($p['post_date'])),
                'blog_link' => strtolower(str_replace(' ', '-', $p['title'])),
            ];
        }

        $output = $view ? $view : null;

        $this->output->json($output);
    }
}