<?php 

/**
 * Admin Pagination Controller Class
 */
class AdminPaginationController extends Controller
{
    public function drawPagination($controls_type) 
    {
        $view['header'] = $this->load->controller('admin/header')->init();
        $view['footer'] = $this->load->controller('admin/footer')->init();
        $view['search'] = $this->load->controller('admin/search')->init();
        $view['nav'] = $this->load->controller('admin/navigation')->init();
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->init();
        $view['controls'] = $this->load->view($controls_type . '/controls');

        return $this->load->view('utilities/list', $view);
    }

    public function getPageTotal($table = null)
    {
        $orderby = $this->load->model('pagination')->checkOrderby($_POST['orderby']);
        $paginated = $this->load->model('pagination')->paginate($table, $orderby, $_POST['direction'], $_POST['page'], $_POST['limit']);

        $this->output->text($paginated['pages']);
    }

    public function getTotalRecordsNumber($table)
    {
        $output = $this->load->model('pagination')->getTotalRecordsNumber($table);
        $this->output->text($output);
    }
}