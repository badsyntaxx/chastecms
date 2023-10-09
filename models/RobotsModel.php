<?php 

/**
 * Robots Model Class
 *
 * Interact with the database to process data related to robots.
 */
class RobotsModel extends Model
{
    public function getRobots()
    {
        // SELECT * FROM `routes`
        $select = $this->table('robots')->select('*')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function insertRobot($data)
    {
        // INSERT INTO `routes` (`id`, `route`) VALUES (?, ?)
        $insert = $this->table('robots')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function deleteRobot($data)
    {
        // DELETE FROM `robots` WHERE `route` = ?
        $delete = $this->table('robots')->delete()->where('route', $data)->execute();
        if ($delete) {
            if ($delete['status'] == 'success') {
                return empty($delete['response']) ? true : $delete['response'];
            } else {
                return false;
            }
        }
    }
}