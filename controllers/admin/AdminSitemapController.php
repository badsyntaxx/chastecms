<?php 

/**
 * Admin Sitemap Controller Class
 *
 * The sitemap controller is responsible for finding all the links in the host. 
 * It also creates an xml version of the sitemap and prepares the data to be 
 * displayed in the view and saved to the database.
 */
class AdminSitemapController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/admin/sitemap
     * - http://root/admin/sitemap/init
     *
     * This method will access other methods in the class to do a few tasks 
     * related to the sitemap and display the sitemap view.
     */
    public function init($view = 'list')
    {
        exit($this->load->controller('admin/pagination')->drawPagination('sitemap'));
    }

    public function setPaginationParams()
    {
        $params = ['table' => 'pages', 'orderby' => 'sitemap_id', 'direction' => 'asc', 'page' => 1, 'limit' => 15];
        $this->output->json($params);
    }

    public function drawTable() 
    {
        $paginated = $this->load->model('pagination')->paginate('sitemap', $_POST['orderby'], $_POST['direction'], $_POST['page'], $_POST['limit']);
        $pages = $paginated['records'];

        $view['pages'] = [];
        foreach ($pages as $page) {
            if ($page['visibility'] == 0) {
                $visibility = 'Visible';
            } elseif ($page['visibility'] == 1) {
                $visibility = 'Hidden';
            }

            $view['pages'][] = [
                'sitemap_id' => $page['sitemap_id'],
                'name' => $page['name'],
                'route_name' => $page['route_name'],
                'route' => $page['route'],
                'sort_order' => $page['sort_order'],
                'visibility' => $visibility,
            ];
        }

        $view['sitemap_xml'] = $this->checkSitemapXMLExists();
        $view['sitemap_records'] = $this->checkSitemapRecordsExist();

        $output = ['table' => $this->load->view('sitemap/list', $view), 'start' => $paginated['start']];
        $this->output->json($output, 'exit');
    }

    public function generate()
    {
        $this->erase(); 
        $this->insertSitemapRecords();
        $this->generateSitemapXML();
        $this->gusto->log('Admin "' . $this->logged_user['username'] . '" reset the sitemap.');
        $this->load->route('/admin/sitemap/list');
    }

    private function erase()
    {
        if (is_file(VIEWS_DIR . '/sitemap.xml')) {
            if (!unlink(VIEWS_DIR . '/sitemap.xml')) {
                echo $this->language->get('sitemap/unable_delete');
            }
        } 
        $this->load->model('sitemap')->eraseSitemap();
    }

    public function hide($bool)
    {
        if ($bool == 'true') {
            $visibility = 1;
            $visibility_text = 'hidden';
            $alert = 'links_hidden';
        } else {
            $visibility = 0;
            $visibility_text = 'visible';
            $alert = 'links_visible';
        }

        $sitemap_model = $this->load->model('sitemap');

        foreach ($_POST as $id) {
            $sitelink = $sitemap_model->getSitelink('sitemap_id', $id);
            if ($sitelink) {
                $data['visibility'] = $visibility;
                $data['sitemap_id'] = $id;
                if ($sitemap_model->updateSitemap('sitemap_id', $data)) {

                }
            }
            $this->gusto->log('Admin "' . $this->logged_user['username'] . '" set the sitemap link "' . $sitelink['name'] . '" to "' . $visibility_text . '".');
        }

        $this->generateSitemapXML();

        $output = ['alert' => 'success', 'message' => $this->language->get('sitemap/' . $alert)];
        $this->output->json($output, 'exit'); 
    }

    public function save()
    {
        foreach ($_POST as $key => $value) {
            $data['sort_order'] = $value;
            $data['name'] = $key;
            $this->load->model('sitemap')->updateSitemap('name', $data);
        }

        $this->gusto->log('Admin "' . $this->logged_user['username'] . '" adjusted the order of links in the sitemap.');

        $this->generateSitemapXML();

        $output = ['alert' => 'success', 'message' => $this->language->get('sitemap/updated')];
        $this->output->json($output, 'exit');
    }

    public function insertSitemapRecords() 
    {
        $pages_model = $this->load->model('pages');
        $sitemap_model = $this->load->model('sitemap');
        $pages = $pages_model->getPages('pages');
        $num = 1;

        foreach ($pages as $p) {
            $route = $pages_model->getRoute('routes_id', $p['pages_id']);
            $data['name'] = $p['name'];
            $data['route_name'] = $this->generateRouteName($p['name']);
            $data['route'] = $route['route'];
            $data['sort_order'] += $num;
            if (!$sitemap_model->getSiteLink('name', $p['name'])) {
                $sitemap_model->insertSiteLink($data);
            }
        }
    }

    public function generateRouteName($name) 
    {
        if (strpos($name, '-')) {
            $name_array = explode('-', $name);
            $route_name = [];
            foreach ($name_array as $na) {
                array_push($route_name, ucfirst($na));
            }
            return implode(' ', $route_name);
        } else {
            return ucfirst($name);
        }
    }

    /**
     * Create the xml sitemap
     * 
     * Using the links found in the findLinks method generate or alter a
     * sitemap.xml file and write the sitemap data to the file.
     */
    private function generateSitemapXML()
    {
        // Open sitemap.xml or create the file if it doesn't exist. If php cannot open or 
        // create the file then exit.
        $sitemap = fopen(VIEWS_DIR . '/sitemap.xml', 'w') or exit('Unable to open file!');
        $sitelinks = $this->load->model('sitemap')->getSiteLinks();

        // Write the beggining of the sitemap.xml.
        fwrite($sitemap, "<?xml version=\"1.0\" encoding=\"UTF-8\"?> \n");
        fwrite($sitemap, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"> \n");

        // Write the links to the sitemap.xml. The spaces in the fwrite are necessary
        // for the xml to be formatted neatly.
        foreach ($sitelinks as $sl) {
            if (!$sl['visibility']) {
                fwrite($sitemap, "    <url>\n        <loc>" . HOST . $sl['route'] . "</loc>\n        <changefreq>weekly</changefreq>\n    </url> \n");
            }
        }

        // Write the ending of the sitemap.xml.
        fwrite($sitemap, '</urlset>');
    }

    public function checkSitemapXMLExists()
    {
        if (file_exists(VIEWS_DIR . '/sitemap.xml')) {
            return true;
        } else {
            return false;
        }
    }

    public function checkSitemapRecordsExist()
    {
        if ($this->load->model('sitemap')->getSiteLinks()) {
            return true;
        } else {
            return false;
        }
    }
}