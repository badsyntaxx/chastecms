<?php 

/**
* Pagination Model
*/
class PaginationModel extends Model
{
    /**
     * Paginate records.
     *
     * @param string $table
     * @param string $orderby
     * @param string $direction
     * @param int $start
     * @param int $limit
     * @param string $where
     * @return array
     */
    public function paginate($table = null, $orderby = null, $direction = null, $start = null, $limit = null, $column = null, $is = null)
    {
        if (isset($start) && isset($limit)) {
            $orderby = $this->checkOrderby($orderby, $table);
            $total = $this->getTotalRecordsNumber($table, $column, $is);
            $total = $total ? $total : 0;
            $pages = ceil($total / $limit);
            $start = ($start-1) * $limit;
            $records = $this->getRecords($table, $orderby, $direction, $start, $limit, $column, $is);
            $records = $records ? $records : [];
            $output = ['pages' => $pages, 'start' => $start, 'records' => $records, 'total' => $total];
            return $output;
        }
    }

    /** 
     * Get total number of records from a given table.
     *
     * @param string $table
     * @return mixed
     */
    public function getTotalRecordsNumber($table, $column, $is)
    {
        if ($column && $is) {
            $select = $this->table($table)->count()->where($column, $is)->get('string');
        } else {
            $select = $this->table($table)->count()->get('string');
        }
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
    public function getRecords($table, $orderby, $direction, $start, $limit, $column, $is)
    {
        if (isset($column) && isset($is)) {
            $select = $this->table($table)->select('*')->where($column, $is)->orderBy($orderby, $direction)->limitBetween($start, $limit)->get();
        } else {
            $select = $this->table($table)->select('*')->orderBy($orderby, $direction)->limitBetween($start, $limit)->get();
        }
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function checkOrderby($orderby, $table)
    {
        switch ($orderby) {
            case 'status':
                if ($table == 'users') {
                    return 'last_active';
                } else {
                    return $orderby;
                }
                break;
            case 'request_id':
                return 'requests_id';
                break; 
            case 'cable_id':
                return 'cable_num';
                break; 
            case 'name':
                return 'firstname';
                break;
            default:
                return $orderby;
                break;
        }
    }
}