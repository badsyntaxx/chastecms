<?php 

/**
 * Users Model Class
 *
 * Interact with the database to process data related to the users.
 */
class UsersModel extends Model
{
    /**
     * Get all user data
     *
     * Get all users data and return their data in arrays.
     * @return array
     */
    public function getUsers()
    {
        // SELECT * FROM `users`
        $select = $this->table('users')->select('*')->get();
        if ($select && $select['status'] == 'success') {
            return empty($select['response']) ? false : $select['response'];
        } else {
            return false;
        }
    }

    /**
     * Get a single users data
     *
     * Get a users data and return their data in an array.
     * @param $param
     * @param $data  
     * @return array
     */
    public function getUser($column, $is)
    {
        // SELECT * FROM `users` WHERE `id` = "2"
        $select = $this->table('users')->select('*')->where($column, $is)->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    /**
     * Insert user into database
     *
     * Insert a new user record into the database.
     * @param array $post      
     * @return bool   
     */
    public function insertUser($post)
    {
        // INSERT INTO `users` (`username`, `email`, `password`, `key`, `last_active`, `ip`) VALUES (?, ?, ?, ?, ?, ?)
        $insert = $this->table('users')->insert($post)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    /**
     * Update a user record
     *
     * @param mixed $data   
     * @return mixed         
     */
    public function updateUser($data, $where)
    {
        // UPDATE `users` SET `username` = ? WHERE `id` = ?
        $update = $this->table('users')->update($data)->where($where)->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                if ($update['affected_rows'] > 0) {
                    return empty($update['response']) ? true : $update['response'];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Insert recovery link
     *
     * This method inserts a recovery link into the database. It expects a
     * email parameter and a token parameter.
     * @param string $email
     * @param string $token    
     */
    public function insertResetLink($data)
    {
        // INSERT INTO `resets` (`email`, `token`) VALUES (?, ?)
        $insert = $this->table('resets')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function getRecoveryLink($token)
    {
        // SELECT * FROM `resets` WHERE `token` = "76e8ed291b483ab7ac87177" LIMIT 1
        $link = $this->table('resets')->select('*')->where('token', $token)->limit(1)->get();
        if ($link) {
            if ($link['status'] == 'success') {
                return empty($link['response']) ? false : $link['response'];
            } else {
                return false;
            }
        }
    }

    public function deleteRecoveryLink($token)
    {
        // DELETE FROM `resets` WHERE `token` = ?
        $delete = $this->table('resets')->delete()->where('token', $token)->execute();
        if ($delete) {
            if ($delete['status'] == 'success') {
                return empty($delete['response']) ? true : $delete['response'];
            } else {
                return false;
            }
        }
    }

    public function getCountries()
    {
        // SELECT * FROM `countries`
        $select = $this->table('countries')->select('*')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? true : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getLoginAttempts($data)
    {
        // SELECT * FROM `logins` WHERE `ip` = "192.168.1.1"
        $select = $this->table('logins')->select('*')->where('ip', $data)->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function insertLoginAttempt($data)
    {
        // INSERT INTO `logins` (`email`, `ip`, `lock_time`, `attempts`) VALUES (?, ?, ?, ?)
        $insert = $this->table('logins')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function updateLoginAttempts($data)
    {
        $update = $this->table('logins')->update($data)->where('ip')->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                if ($update['affected_rows'] > 0) {
                    return empty($update['response']) ? true : $update['response'];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function deleteLoginAttempts($email)
    {
        // DELETE FROM `logins` WHERE `email` = 'person@place.com'
        $delete = $this->table('logins')->delete()->where('email', $email)->execute();
        if ($delete) {
            if ($delete['status'] == 'success') {
                return empty($delete[1]) ? true : $delete[1];
            } else {
                return false;
            }
        }
    }

    public function getTotalUsersNumber()
    {
        // SELECT * FROM `users`
        $select = $this->table('users')->select('*')->count()->get('string');
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getUsersByGroup($data)
    {
        // SELECT * FROM `users` WHERE `id` = ?
        $select = $this->table('users')->select('*')->count()->where('group', $data)->get('string');
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getLatestRegistrant()
    {
        // SELECT * FROM `users` ORDER BY `id` DESC LIMIT 1
        $select = $this->table('users')->select('username, signup_date')->orderby('users_id', 'desc')->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function deleteUser($id)
    {
        // DELETE FROM `users` WHERE `id` = ?
        $delete = $this->table('users')->delete()->where('users_id', $id)->execute();
        if ($delete) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserMenuSetting($is)
    {
        // SELECT * FROM `users` WHERE `id` = "2"
        $select = $this->table('menus')->select('main_menu')->where('menu_anchor', $is)->limit(1)->get('string');   
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    /**
     * Update a user record
     *
     * @param mixed $data   
     * @return mixed         
     */
    public function updateUserMenuSetting($data)
    {
        // UPDATE `users` SET `username` = ? WHERE `id` = ?
        $update = $this->table('menus')->update($data)->where('menu_anchor')->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                if ($update['affected_rows'] > 0) {
                    return empty($update['response']) ? true : $update['response'];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Insert user into database
     *
     * Insert a new user record into the database.
     * @param array $post      
     * @return bool   
     */
    public function createUserMenuSetting($users_id)
    {
        $data['menu_anchor'] = $users_id;
        $data['main_menu'] = 0;
        $insert = $this->table('menus')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }
}