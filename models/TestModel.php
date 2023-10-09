<?php 

/**
 * Test Model
 *
 * 
 */
class TestModel extends Model
{
    public function testQuery()
    {
        $select = $this->table('blog')
        ->select('blog_id, author, title, body, preview_image, post_date, username')
        ->innerJoin('users', 'author', 'users_id')
        ->orderBy('author', 'asc')
        ->limit(3)
        ->testSelect();

        var_dump($select);
    }
}
