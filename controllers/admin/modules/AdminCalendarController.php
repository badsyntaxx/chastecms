<?php

class AdminCalendarController extends Controller
{
    /**
     * Init method
     *
     * The index methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/admin/calendar
     * - http://root/admin/calendar/index
     *
     * This method will load the calendar list table.
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
        $view['controls'] = $this->load->view('modules/calendar/controls');
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

    public function prepareTable($table = 'calendar', $orderby = 'event_id', $direction = 'asc', $page = 1, $record_limit = 15, $column = null, $is = null)
    {
        $paginated = $this->load->model('pagination')->paginate($table, $orderby, $direction, $page, $record_limit, $column, $is);

        $view['events'] = [];

        foreach ($paginated['records'] as $event) {
              
            $view['events'][] = [
                'event_id' => $event['event_id'],
                'event' => $event['event'],
                'start_date' => date('d M, Y', strtotime($event['start_date'])),
                'end_date' => date('d M, Y', strtotime($event['end_date'])),
                'color' => $event['color']
            ];
        }

        $output = [
            'list' => $this->load->view('modules/calendar/list', $view),
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
        $data = $this->prepareTable('calendar', $orderby, $direction, $page, $record_limit, $column, $is);
        
        $output = [
            'list' => $data['list'], 
            'page' => $data['page'], 
            'start' => $data['start'],
            'total_pages' => $data['total_pages'],
            'total_records' => $data['total_records']
        ];

        $this->output->json($output, 'exit');
    }

    public function calendar()
    {
        $view['header'] = $this->load->controller('admin/header')->index();
        $view['footer'] = $this->load->controller('admin/footer')->index();
        $view['search'] = $this->load->controller('admin/search')->index();
        $view['nav'] = $this->load->controller('admin/navigation')->index();
        $view['main_nav'] = $this->session->getSession('main_nav');
        $view['breadcrumb'] = $this->load->controller('admin/breadcrumb')->index();

        exit($this->load->view('modules/calendar/calendar', $view));
    }

    public function getCalendar()
    {
        $date = !empty($_POST['date']) ? $_POST['date'] : null;
        $calendar = $this->load->library('calendar');
        $calendar = $calendar->getCalendar($date);

        $this->output->json($calendar, 'exit');
    }
}
