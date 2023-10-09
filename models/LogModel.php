<?php 

/**
 * Log Model Class
 *
 * Interact with the database to process data related to the log.
 */
class LogModel extends Model
{
    /**
     * Insert a new log record into the database.
     *
     * @param array $post      
     * @return bool   
     */
    public function insertLog($string)
    {
        $data['time'] = date('c');
        $data['event'] = $string;
        $insert = $this->table('log')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    /**
     * Get all log records.
     *
     * @param string $column
     * @param mixed $is
     * @return mixed
     */
    public function getLogs()
    {
        $select = $this->table('log')->select('*')->orderby('log_id', 'desc')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    /**
     * Delete all log records.
     *
     * @return bool
     */
    public function clearLog()
    {
        if ($this->truncate('log')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all error records.
     *
     * @return mixed
     */
    public function getErrors()
    {
        $select = $this->table('errors')->select('*')->orderby('errors_id', 'desc')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    /**
     * Delete all error records.
     *
     * @return void
     */
    public function clearErrors()
    {
        if ($this->truncate('errors')) {
            return true;
        } else {
            return false;
        }
    }
}