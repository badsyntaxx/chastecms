<?php 

/**
 * Navigation Model
 *
 *
 * 
 *
 */
class NavigationModel extends Model
{
    public function getNavLinks()
    {
        // SELECT * FROM `navigation` ORDER BY `sort_order` ASC
        $select = $this->table('navigation')->select('*')->orderBy('sort_order', 'asc')->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getNavLink($param, $data)
    {
        // SELECT * FROM `navigation` WHERE `name` = "home" LIMIT 1
        $select = $this->table('navigation')->select('*')->where($param, $data)->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getTopNavLinks($param)
    {

        $select = $this->table('navigation')->select('*')->where('top', 1)->andWhereNot('route', $param)->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getNavLinkName($param, $data)
    {
        // SELECT * FROM `navigation` WHERE `name` = "home" LIMIT 1
        $select = $this->table('navigation')->select('name')->where($param, $data)->limit(1)->getOne();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function insertNavData($data)
    {
        // INSERT INTO `navigation` (`title`, `name`, `route`, `controller_file`, `last_edit`) VALUES (?, ?, ?, ?, ?)
        $insert = $this->table('navigation')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert[1]) ? true : $insert[1];
            } else {
                return false;
            }
        }
    }

    public function updateNavLink($where, $data)
    {
        $update = $this->table('navigation')->update($data)->where($where)->execute();
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

    public function deleteNavLink($id)
    {
        $delete = $this->table('navigation')->delete()->where('nav_anchor', $id)->execute();
        if ($delete) {
            if ($delete['status'] == 'success') {
                return empty($delete['response']) ? true : $delete['response'];
            } else {
                return false;
            }
        }
    }

    public function getHighestSortNumber()
    {
        $select = $this->table('navigation')->select('sort_order')->orderBy('sort_order', 'desc')->limit(1)->getOne();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

}
