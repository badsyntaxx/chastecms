<?php 

/**
 * Search Model Class
 *
 * This class interact with the database and tries to find data that matches 
 * the users search string.
 */
class SearchModel extends Model
{
    public function searchUsers($data)
    {
        // SELECT * FROM `users` WHERE `username` LIKE "%billy%" OR `firstname` LIKE "%billy%" OR `lastname` LIKE "%billy%" OR `email` LIKE "%billy%"
        $select = $this->table('users')->select('*')->whereLike('username', $data)->orWhereLike('firstname', $data)->orWhereLike('lastname', $data)->orWhereLike('email', $data)->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                if (!empty($select['response'])) {
                    foreach ($select['response'] as $user) {
                        $users[] = [
                            'username' => $user['username'],
                            'avatar' => $user['avatar'],
                            'country' => $user['country']
                        ];
                    }

                    return $users;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function searchBlog($data)
    {
        // SELECT * FROM `blog` WHERE `title` LIKE "%test%" OR `author` LIKE "%test%"
        $select = $this->table('blog')->select('*')->whereLike('title', $data)->orWhereLike('author', $data)->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                if (!empty($select['response'])) {
                    foreach ($select['response'] as $post) {
                        $body = preg_replace('/<img[^>]+\>/i', '', $post['body']);
                        $body = preg_replace('/<p[^>]*>[\s|&nbsp;]*<\/p>/', '', $body);
                        $posts[] = [
                            'id' => $post['blog_id'],
                            'author' => $post['author'],
                            'title' => $post['title'],
                            'body' => $body,
                            'preview_image' => $post['preview_image'],
                            'post_date' => date('l, j M, Y', strtotime($post['post_date'])),
                            'blog_link' => strtolower(str_replace(' ', '-', $post['title'])),
                        ];
                    }

                    return $posts;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function liveSearchUsers($data)
    {
        // SELECT * FROM `users` WHERE `username` LIKE "%test%"
        $select = $this->table('users')->select('*')->whereLike('username', $data)->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function liveSearchBlogPosts($data)
    {
        // SELECT * FROM `blog` WHERE `title` LIKE "%test%"
        $select = $this->table('blog')->select('*')->whereLike('title', $data)->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function liveSearchPages($data)
    {
        // SELECT * FROM `pages` WHERE `name` LIKE "%test%"
        $select = $this->table('pages')->select('*')->whereLike('name', $data)->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }
}