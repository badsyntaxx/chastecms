<?php 

class AnalyticsModel extends Model
{
    public function insertAnalyticsCode($data)
    {
        $insert = $this->table('analytics')->insert($data)->execute();
        if ($insert) {
            if ($insert['status'] == 'success') {
                return empty($insert['response']) ? true : $insert['response'];
            } else {
                return false;
            }
        }
    }

    public function getAnalyticsCode()
    {
        $select = $this->table('analytics')->select('code')->get('string');
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function updateAnalyticsCode($data)
    {
        $update = $this->table('analytics')->update($data)->where('analytics_id')->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                if ($update['affected_rows'] > 0) {
                    return empty($update['response']) ? true : $update['response'];
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }
}
