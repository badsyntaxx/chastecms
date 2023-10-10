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
        $select = $this->table('blog')->select('blog_id, post_date, body, username')->innerJoin('users', 'author', 'users_id')->where($param, $data)->limit(1)->testSelect();

        var_dump($select);
    }
}
