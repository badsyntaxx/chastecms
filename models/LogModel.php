<?php 

/**
 * Log Model Class
 *
 * Interact with the database to process data related to the log.
 */
class LogModel extends Model
{
    /**
     * Insert log into database
     *
     * Insert a new log record into the database.
     * @param array $post      
     * @return bool   
     */
    public function insertLog($string)
    {
        $data['time'] = date('c');
        $data['event'] = $string;
        // INSERT INTO `log` (`id`, `time`, `event`) VALUES (?, ?, ?)
        $insert = $this->table('log')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function getLogs()
    {
        // SELECT * FROM `log` ORDER BY `id` DESC
        $select = $this->table('log')->select('*')->orderby('log_id', 'desc')->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function clearLog()
    {
        // TRUNCATE TABLE `log`
        if ($this->truncate('log')) {
            return true;
        } else {
            return false;
        }
    }

    public function getErrors()
    {
        // SELECT * FROM `errors` ORDER BY `id` DESC
        $select = $this->table('errors')->select('*')->orderby('errors_id', 'desc')->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function clearErrors()
    {
        // TRUNCATE TABLE `errors`
        if ($this->truncate('errors')) {
            return true;
        } else {
            return false;
        }
    }
}