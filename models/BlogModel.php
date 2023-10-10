<?php 

/**
 * Blog Model Class
 *
 * Interact with the database to process data related to the blog.
 */
class BlogModel extends Model
{
    public function getPosts($limit)
    {
        // SELECT * FROM `blog` ORDER BY `id` DESC LIMIT 10
        $select = $this->table('blog')->select('blog_id, author, title, body, preview_image, post_date, username')->innerJoin('users', 'author', 'users_id')->orderBy('author', 'asc')->limit(3)->getAll();
        if ($select && $select['status'] == 'success') {
            return empty($select['response']) ? false : $select['response'];
        } else {
            return false;
        }
    }

    public function getPost($param, $data)
    {
        // 'SELECT `blog_id`, `post_date`, `body`, `username` FROM `blog` INNER JOIN `users` ON blog.author = users.users_id WHERE `blog_id` = "1" LIMIT 1'
        $select = $this->table('blog')->select('blog_id, post_date, body, username')->innerJoin('users', 'author', 'users_id')->where($param, $data)->limit(1)->get();
        if ($select && $select['status'] == 'success') {
            return empty($select['response']) ? false : $select['response'];
        } else {
            return false;
        }
    }

    public function updateBlogStatistics($id)
    {
        // SELECT `views` FROM `blog` WHERE `id` = "1"
        $select = $this->table('blog')->select('views')->where('blog_id', $id)->limit(1)->getOne();
        if ($select) {
            if ($select['status'] == 'success') {
                $views = $select['response'];
                $views += 1;
            } else {
                return false;
            }
        }

        $data['views'] = $views;
        $data['last_view'] = date('c');
        $data['blog_id'] = $id;
        // UPDATE `blog` SET `views` = ? WHERE `id` = ?
        $update = $this->table('blog')->update($data)->where('blog_id')->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                return empty($update['response']) ? true : $update['response'];
            } else {
                return false;
            }
        }
    }

    public function insertPost($data)
    {
        // INSERT INTO `blog` (`author`, `title`, `body`, `preview_image`) VALUES (?, ?, ?, ?)
        $insert = $this->table('blog')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function updateBlog($data)
    {
        // UPDATE `blog` SET `title` = ?, `body` = ?, `preview_image` = ?, `last_edit` = ? WHERE `id` = ?
        $update = $this->table('blog')->update($data)->where('blog_id')->execute();
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

    public function deletePost($id)
    {
        // DELETE FROM `blog` WHERE `id` = ?
        $delete = $this->table('blog')->delete()->where('blog_id', $id)->execute();
        if ($delete) {
            return true;
        } else {
            return false;
        }
    }

    public function getTotalPostsNumber()
    {
        // SELECT * FROM `blog`
        $select = $this->table('blog')->select('*')->getTotal();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getViewsNumber()
    {
        // SELECT `views` FROM `blog`
        $select = $this->table('blog')->select('views')->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                if (!empty($select['response'])) {
                    $num = 0;
                    foreach ($select['response'] as $total) {
                        $num = $total['views'] + $num;
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

    public function getMostViewed()
    {
        // SELECT * FROM `blog` WHERE `views` = (SELECT max(views) FROM `blog`)
        $select = $this->table('blog')->select('*')->whereHighest('views')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getLastPost()
    {
        // SELECT * FROM `blog` ORDER BY `post_date` DESC LIMIT 1
        $select = $this->table('blog')->select('*')->orderby('post_date', 'desc')->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getLastEdited()
    {
        // SELECT * FROM `blog` ORDER BY `last_edit` ASC LIMIT 1
        $select = $this->table('blog')->select('*')->orderby('last_edit', 'desc')->limit(1)->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }
}