<?php 

/**
 * Pages Model Class
 *
 * Interact with the database to process data related to pages.
 */
class PagesModel extends Model
{
    public function getPages()
    {
        // SELECT * FROM `pages` WHERE `name` = "home"
        $select = $this->table('pages')->select('*')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? [] : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getPage($param, $data)
    {
        // SELECT * FROM `pages` WHERE `name` = "home"
        $select = $this->table('pages')->select('*')->where($param, $data)->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getPageContent($data)
    {
        // SELECT `content` FROM `pages` WHERE `name` = "home" LIMIT 1
        $select = $this->table('pages')->select('content')->where('name', $data)->limit(1)->get('string');
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function updatePageStatistics($name)
    {
        // SELECT `views` FROM `pages` WHERE `name` = "thing" LIMIT 1
        $select = $this->table('pages')->select('views')->where('name', $name)->limit(1)->get('string');
        if ($select) {
            if ($select['status'] == 'success') {
                $data['views'] = ++$select['response'];
                $data['last_view'] = date('c');
                $data['name'] = $name;
            } else {
                return false;
            }
        }

        // UPDATE `PAGES` SET `views` = ? WHERE `name` = ?
        $update = $this->table('pages')->update($data)->where('name')->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                return empty($update['response']) ? true : $update['response'];
            } else {
                return false;
            }
        }
    }

    public function insertPage($data)
    {
        // INSERT INTO `pages` (`title`, `name`, `route`, `controller_file`, `last_edit`) VALUES (?, ?, ?, ?, ?)
        $insert = $this->table('pages')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function updatePage($data)
    {
        // UPDATE `pages` SET `content` = ?, `last_edit` = ? WHERE `name` = ?
        $update = $this->table('pages')->update($data)->where('pages_id')->execute();
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

    public function getLastPageMade()
    {
        // SELECT * FROM `pages` ORDER BY `creation_date` DESC LIMIT 1
        $select = $this->table('pages')->select('*')->orderby('creation_date', 'desc')->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function deletePage($id)
    {
        // DELETE FROM `pages` WHERE `id` = ?
        $delete = $this->table('pages')->delete()->where('pages_id', $id)->execute();
        if ($delete) {
            if ($delete['status'] == 'success') {
                return empty($delete['response']) ? true : $delete['response'];
            } else {
                return false;
            }
        }
    }

    public function getTotalPageNumber()
    {
        // SELECT * FROM `pages`
        $select = $this->table('pages')->select('*')->count()->get('string');
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getMostViewed()
    {
        // SELECT * FROM `pages` WHERE `views` = (SELECT max(views) FROM `pages`)
        $select = $this->table('pages')->select('*')->highest('views')->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getTotalPageViews()
    {
        // SELECT * FROM `pages`
        $select = $this->table('pages')->select('views')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                if (!empty($select['response'])) {
                    $num = 0;
                    foreach ($select['response'] as $total) {
                        $num += $total['views'];
                    }
                    return $num;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function getLastEdited()
    {
        // SELECT * FROM `pages` ORDER BY `last_edit` ASC LIMIT 1
        $select = $this->table('pages')->select('*')->orderby('last_edit', 'desc')->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function insertRoute($data)
    {
        // INSERT INTO `routes` (`pages_id`, `route`, `controller`) VALUES (?, ?, ?)
        $insert = $this->table('routes')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function getRoute($param, $data)
    {
        // SELECT * FROM `routes` WHERE `route` = "/home"
        $select = $this->table('routes')->select('*')->where($param, $data)->orderBy('routes_id', 'asc')->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getRoutes($param, $data)
    {
        // SELECT * FROM `routes` WHERE `route` = "/home"
        $select = $this->table('routes')->select('*')->where($param, $data)->orderBy('routes_id', 'asc')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function deleteRoute($param, $data)
    {
        // DELETE FROM `routes` WHERE `id` = ?
        $delete = $this->table('routes')->delete()->where($param, $data)->execute();
        if ($delete) {
            if ($delete['status'] == 'success') {
                return empty($delete['response']) ? true : $delete['response'];
            } else {
                return false;
            }
        }
    }

    public function updateRoute($param, $data)
    {
        // UPDATE `routes` SET `route` = ? WHERE `pages_id` = ?
        $update = $this->table('routes')->update($data)->where($param)->execute();
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

    public function getSpecificPageData($select)
    {
        $select = $this->table('pages')->select($select)->get();
        $array = [];
        if ($select) {
            if ($select['status'] == 'success') {
                if (!empty($select['response'])) {
                    foreach ($select['response'] as $r) {
                        array_push($array, implode($r));
                    }
                    return $array;
                } else {
                    return false;
                }
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }
}