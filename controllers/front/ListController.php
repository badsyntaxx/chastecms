<?php 

/**
 *  List Controller Class
 */
class ListController extends Controller
{
    public function drawList() 
    {
        $view['header'] = $this->load->controller('header')->init();
        $view['footer'] = $this->load->controller('footer')->init();

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