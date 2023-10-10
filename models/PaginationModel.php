<?php 

/**
* Pagination Model
*/
class PaginationModel extends Model
{
    /**
     * Paginate records
     */
    public function paginate($table = null, $orderby = null, $direction = null, $page = null, $limit = null)
    {
        if (isset($page) && isset($limit)) {
            $orderby = $this->checkOrderby($orderby);
            $total = $this->getTotalRecordsNumber($table);
            $pages = ceil($total/$limit);
            $start = ($page-1) * $limit;
            $records = $this->getRecords($table, $orderby, $direction, $start, $limit);
            $output = ['pages' => $pages, 'start' => $start, 'records' => $records];
            return $output;
        }
    }

    /** 
     * Get total number or records from a given table.
     */ 
    public function getTotalRecordsNumber($table)
    {
        // SELECT * FROM `pages`
        $select = $this->table($table)->select('*')->getTotal();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    /**
     * Get some records
     *
     * @return array
     */
    public function getRecords($table, $orderby, $direction, $start, $limit)
    {
        // SELECT * FROM `users` ORDER BY `id` ASC LIMIT 5, 10
        $select = $this->table($table)->select('*')->orderBy($orderby, $direction)->limitBetween($start, $limit)->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function checkOrderby($orderby)
    {
        switch ($orderby) {
            case 'status':
                return 'last_active';
                break;
            case 'last_viewed':
                return 'last_view';
                break;
            case 'posted':
                return 'post_date';
                break; 
            case 'order':
                return 'sort_order';
                break;  
            default:
                return $orderby;
                break;
        }
    }
}