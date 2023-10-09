<?php 

/**
 * Private Message Model Class
 *
 * Interact with the database to process data related to private messages.
 */
class MessageModel extends Model
{
    public function getMessages($param, $data)
    {
        $select = $this->table('messages')->select('*')->where($param, $data)->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getMessage($param, $data)
    {
        // SELECT * FROM `messages` WHERE `id` = "2"
        $select = $this->table('messages')->select('*')->where($param, $data)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getChainId()
    {
        $query = $this->con->mysqli->query('SELECT max(`chain_id`) FROM `messages`');
        $array = $query->fetch_assoc();
        $chain_id = $array['max(`chain_id`)'];
        return $chain_id;
    }

    public function insertMessage($chain_id, $subject, $sender, $receiver, $message)
    {
        $query = $this->con->mysqli->prepare('INSERT INTO `messages` (`chain_id`, `subject`, `sender`, `receiver`, `message`) VALUES(?, ?, ?, ?, ?)');
        $query->bind_param('sssss', $chain_id, $subject, $sender, $receiver, $message);
        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }
}